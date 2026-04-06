<?php

namespace App\Http\Controllers\Filament;

use EslamRedaDiv\FilamentCopilot\Agent\CopilotAgent;
use EslamRedaDiv\FilamentCopilot\Events\CopilotMessageSent;
use EslamRedaDiv\FilamentCopilot\Events\CopilotResponseReceived;
use EslamRedaDiv\FilamentCopilot\FilamentCopilotPlugin;
use EslamRedaDiv\FilamentCopilot\Http\Controllers\StreamController as BaseStreamController;
use EslamRedaDiv\FilamentCopilot\Models\CopilotConversation;
use EslamRedaDiv\FilamentCopilot\Services\ConversationManager;
use EslamRedaDiv\FilamentCopilot\Services\RateLimitService;
use EslamRedaDiv\FilamentCopilot\Services\ToolRegistry;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Streaming\Events\StreamEnd;
use Laravel\Ai\Streaming\Events\TextDelta;
use Laravel\Ai\Streaming\Events\ToolCall;
use Laravel\Ai\Streaming\Events\ToolResult;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CopilotStreamController extends BaseStreamController
{
    public function stream(Request $request): StreamedResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:10000'],
            'conversation_id' => ['nullable', 'string'],
            'panel_id' => ['required', 'string'],
        ]);

        $panelId = $request->input('panel_id');

        try {
            Filament::setCurrentPanel($panelId);
        } catch (\Throwable) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $user = Filament::auth()->user();

        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $tenant = Filament::getTenant();
        $content = $request->input('message');
        $conversationId = $request->input('conversation_id');

        /** @var RateLimitService $rateLimitService */
        $rateLimitService = app(RateLimitService::class);

        if (config('filament-copilot.rate_limits.enabled') && ! $rateLimitService->canSendMessage($user, $panelId, $tenant)) {
            return $this->sseResponse(function () {
                $this->sendSseEvent('error', ['message' => __('filament-copilot::filament-copilot.rate_limit_exceeded')]);
                $this->sendSseEvent('done', []);
            });
        }

        /** @var ConversationManager $conversationManager */
        $conversationManager = app(ConversationManager::class);

        if ($conversationId) {
            $conversation = CopilotConversation::query()
                ->forPanel($panelId)
                ->forParticipant($user)
                ->forTenant($tenant)
                ->find($conversationId);

            if (! $conversation) {
                return $this->sseResponse(function () {
                    $this->sendSseEvent('error', ['message' => 'Conversation not found.']);
                    $this->sendSseEvent('done', []);
                });
            }
        } else {
            $conversation = $conversationManager->create($user, $panelId, $tenant);
        }

        $conversationManager->addUserMessage($conversation, $content);
        event(new CopilotMessageSent($conversation, $content, $panelId));

        return $this->sseResponse(function () use ($conversation, $conversationManager, $user, $panelId, $tenant, $rateLimitService) {
            $this->sendSseEvent('conversation', ['id' => $conversation->id]);

            try {
                /** @var ToolRegistry $toolRegistry */
                $toolRegistry = app(ToolRegistry::class);

                /** @var CopilotAgent $agent */
                $agent = app(CopilotAgent::class);

                $messages = $conversationManager->getMessagesForAgent($conversation);

                /** @var FilamentCopilotPlugin $plugin */
                $plugin = FilamentCopilotPlugin::get();

                $agent->forPanel($panelId)
                    ->forUser($user)
                    ->forTenant($tenant)
                    ->withTools($toolRegistry->buildTools($panelId, $user, $tenant, $conversation->id))
                    ->withMessages($messages)
                    ->withSystemPrompt($plugin->getSystemPrompt());

                $provider = $plugin->getProvider();
                $model = $plugin->getModel();

                $lastUserMessage = '';
                foreach ($messages as $message) {
                    if ($message['role'] === 'user') {
                        $lastUserMessage = $message['content'];
                    }
                }

                $this->sendSseEvent('start', []);

                if ($this->shouldUseBufferedResponse($provider)) {
                    $response = $agent->prompt(
                        prompt: $lastUserMessage,
                        provider: $provider,
                        model: $model,
                    );

                    $responseText = $response->text;
                    $this->emitBufferedResponse($response);
                    $usage = $response->usage;
                } else {
                    $streamResponse = $agent->stream(
                        prompt: $lastUserMessage,
                        provider: $provider,
                        model: $model,
                    );

                    $responseText = '';
                    $usage = null;

                    foreach ($streamResponse as $event) {
                        if ($event instanceof TextDelta) {
                            $responseText .= $event->delta;
                            $this->sendSseEvent('chunk', ['text' => $event->delta]);
                        } elseif ($event instanceof ToolCall) {
                            $this->sendSseEvent('tool_call', [
                                'tool_id' => $event->toolCall->id,
                                'tool_name' => $event->toolCall->name,
                                'arguments' => $event->toolCall->arguments,
                            ]);
                        } elseif ($event instanceof ToolResult) {
                            $rawResult = is_string($event->toolResult->result) ? $event->toolResult->result : json_encode($event->toolResult->result);

                            $this->sendSseEvent('tool_result', [
                                'tool_id' => $event->toolResult->id ?? '',
                                'tool_name' => $event->toolResult->name ?? '',
                                'result' => $rawResult,
                                'success' => $event->successful,
                                'error' => $event->error,
                            ]);
                        } elseif ($event instanceof StreamEnd) {
                            $usage = $event->usage;
                        }
                    }

                    if ($usage === null) {
                        $usage = $streamResponse->usage;
                    }
                }

                $assistantMessage = $conversationManager->addAssistantMessage(
                    conversation: $conversation,
                    content: $responseText,
                    inputTokens: $usage->promptTokens ?? 0,
                    outputTokens: $usage->completionTokens ?? 0,
                );

                if (config('filament-copilot.rate_limits.enabled')) {
                    $rateLimitService->recordTokenUsage(
                        user: $user,
                        panelId: $panelId,
                        inputTokens: $usage->promptTokens ?? 0,
                        outputTokens: $usage->completionTokens ?? 0,
                        tenant: $tenant,
                        conversationId: $conversation->id,
                        model: $model,
                        provider: $provider,
                    );
                }

                event(new CopilotResponseReceived(
                    $conversation,
                    $assistantMessage,
                    $usage->promptTokens ?? 0,
                    $usage->completionTokens ?? 0,
                ));

                $this->sendSseEvent('usage', [
                    'input_tokens' => $usage->promptTokens ?? 0,
                    'output_tokens' => $usage->completionTokens ?? 0,
                ]);

                $this->sendSseEvent('done', []);
            } catch (\Throwable $exception) {
                logger()->error('Copilot stream failed.', [
                    'provider' => FilamentCopilotPlugin::get()->getProvider(),
                    'model' => FilamentCopilotPlugin::get()->getModel(),
                    'message' => $exception->getMessage(),
                ]);

                $this->sendSseEvent('error', ['message' => $exception->getMessage()]);
                $this->sendSseEvent('done', []);
            }
        });
    }

    protected function shouldUseBufferedResponse(string $provider): bool
    {
        return $provider === 'gemini';
    }

    protected function emitBufferedResponse(AgentResponse $response): void
    {
        foreach ($response->toolCalls as $toolCall) {
            $this->sendSseEvent('tool_call', [
                'tool_id' => $toolCall->id ?? '',
                'tool_name' => $toolCall->name ?? 'tool',
                'arguments' => $toolCall->arguments ?? [],
            ]);
        }

        foreach ($response->toolResults as $toolResult) {
            $rawResult = is_string($toolResult->result ?? null) ? $toolResult->result : json_encode($toolResult->result ?? null);

            $this->sendSseEvent('tool_result', [
                'tool_id' => $toolResult->toolCallId ?? '',
                'tool_name' => $toolResult->toolName ?? 'tool',
                'result' => $rawResult,
                'success' => true,
                'error' => null,
            ]);
        }

        if ($response->text !== '') {
            $this->sendSseEvent('chunk', ['text' => $response->text]);
        }
    }
}
