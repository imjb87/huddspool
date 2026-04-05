<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class GoogleController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->withErrors(['google' => 'Google sign in could not be completed. Please try again.']);
        }

        $user = User::query()
            ->where('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            return redirect()
                ->route('login')
                ->withErrors(['google' => 'No account matches the email address on that Google account.']);
        }

        $this->storeGoogleAvatarForUser($user, $googleUser->getAvatar());

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect($user->getRedirectRoute());
    }

    private function storeGoogleAvatarForUser(User $user, ?string $avatarUrl): void
    {
        if ($user->hasAvatar() || ! $avatarUrl) {
            return;
        }

        try {
            $response = Http::timeout(10)->get($avatarUrl);
        } catch (Throwable) {
            return;
        }

        if (! $response->successful()) {
            return;
        }

        $contentType = (string) $response->header('Content-Type', '');

        if (! str_starts_with($contentType, 'image/')) {
            return;
        }

        $extension = match ($contentType) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };

        $user->replaceAvatarWithContents(
            $response->body(),
            'google-'.$user->getKey().'-'.Str::random(12).'.'.$extension,
        );
    }
}
