<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class FacebookController extends Controller
{
    public function redirectToFacebook(): RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback(): RedirectResponse
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->withErrors(['facebook' => 'Facebook sign in could not be completed. Please try again.']);
        }

        $user = User::query()
            ->where('email', $facebookUser->getEmail())
            ->first();

        if (! $user) {
            return redirect()
                ->route('login')
                ->withErrors(['facebook' => 'No account matches the email address on that Facebook account.']);
        }

        $this->storeFacebookAvatarForUser($user, $facebookUser->getAvatar());

        Auth::login($user);
        request()->session()->regenerate();

        return redirect($user->getRedirectRoute());
    }

    private function storeFacebookAvatarForUser(User $user, ?string $avatarUrl): void
    {
        if ($user->avatar_path || ! $avatarUrl) {
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

        $path = 'avatars/facebook-'.$user->getKey().'-'.Str::random(12).'.'.$extension;

        Storage::disk('public')->put($path, $response->body());

        $user->forceFill([
            'avatar_path' => $path,
        ])->save();
    }
}
