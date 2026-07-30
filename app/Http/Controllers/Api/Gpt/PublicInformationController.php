<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gpt\BrowsePublicInformationRequest;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicInformationController extends Controller
{
    public function __invoke(BrowsePublicInformationRequest $request, Kernel $kernel): JsonResponse
    {
        $path = $request->validated('path');
        $administrator = $request->user();
        Auth::forgetGuards();
        $subRequest = Request::create($path, 'GET');
        $subRequest->headers->set('Accept', 'text/html');
        $response = $kernel->handle($subRequest);
        Auth::guard('api')->setUser($administrator);

        if ($response->getStatusCode() >= 400) {
            return response()->json(['message' => 'The public page could not be retrieved.'], $response->getStatusCode());
        }

        $html = $response->getContent();
        preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $titleMatch);
        preg_match_all('/href=["\']([^"\']+)["\']/i', $html, $linkMatches);
        $text = html_entity_decode(strip_tags(preg_replace('/<(script|style)[^>]*>.*?<\/\1>/is', ' ', $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        return response()->json([
            'path' => $path,
            'title' => trim(html_entity_decode(strip_tags($titleMatch[1] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
            'content' => $text,
            'links' => collect($linkMatches[1] ?? [])->filter(fn (string $link): bool => str_starts_with($link, '/') || str_starts_with($link, config('app.url')))->unique()->values(),
        ]);
    }
}
