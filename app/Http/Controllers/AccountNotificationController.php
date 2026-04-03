<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class AccountNotificationController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json($this->summaryPayload($user));
    }

    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->unreadNotifications()->update([
            'read_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json($this->summaryPayload($user));
        }

        return redirect()->back();
    }

    public function markAsRead(Request $request, string $notification): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var ?DatabaseNotification $record */
        $record = $user->notifications()
            ->whereKey($notification)
            ->first();

        abort_unless($record instanceof DatabaseNotification, 404);

        if (! $record->read_at) {
            $record->markAsRead();
        }

        if ($request->expectsJson()) {
            return response()->json($this->summaryPayload($user));
        }

        return redirect()->back();
    }

    public function open(Request $request, string $notification): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var ?DatabaseNotification $record */
        $record = $user->notifications()
            ->whereKey($notification)
            ->first();

        abort_unless($record instanceof DatabaseNotification, 404);

        if (! $record->read_at) {
            $record->markAsRead();
        }

        return redirect(data_get($record->data, 'action_url', route('account.show')));
    }

    /**
     * @return array{
     *     unread_count: int,
     *     notifications: array<int, array{
     *         id: string,
     *         title: string,
     *         body: ?string,
     *         action_url: string,
     *         open_url: string,
     *         read: bool,
     *         created_at_human: string
     *     }>
     * }
     */
    private function summaryPayload(User $user): array
    {
        return [
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $user->notifications()
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn (DatabaseNotification $notification): array => $this->mapNotification($notification))
                ->all(),
        ];
    }

    /**
     * @return array{
     *     id: string,
     *     title: string,
     *     body: ?string,
     *     action_url: string,
     *     open_url: string,
     *     read: bool,
     *     created_at_human: string
     * }
     */
    private function mapNotification(DatabaseNotification $notification): array
    {
        return [
            'id' => (string) $notification->getKey(),
            'title' => (string) data_get($notification->data, 'title', 'Notification'),
            'body' => data_get($notification->data, 'body'),
            'action_url' => (string) data_get($notification->data, 'action_url', route('account.show')),
            'open_url' => route('account.notifications.open', $notification->id),
            'read' => $notification->read_at !== null,
            'created_at_human' => $notification->created_at?->diffForHumans() ?? '',
        ];
    }
}
