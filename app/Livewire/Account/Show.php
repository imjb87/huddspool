<?php

namespace App\Livewire\Account;

use App\Queries\GetPlayerAverages;
use App\Queries\GetPlayerFrames;
use App\Queries\GetPlayerKnockoutMatches;
use App\Queries\GetPlayerSeasonHistory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Show extends BaseAccountComponent
{
    use AuthorizesRequests;
    use WithFileUploads;

    public ?TemporaryUploadedFile $avatarUpload = null;

    public ?string $email = null;

    public ?string $telephone = null;

    public bool $removeAvatar = false;

    public function mount(): void
    {
        $this->email = $this->user->email;
        $this->telephone = $this->user->telephone;
    }

    public function saveProfile(): void
    {
        $this->authorize('updateProfile', $this->user);

        $validated = $this->validate([
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user->id)],
            'telephone' => ['nullable', 'string', 'max:255'],
            'avatarUpload' => ['nullable', 'image', 'max:5120'],
        ]);

        $avatarPath = $this->user->avatar_path;

        if ($this->removeAvatar) {
            if ($this->user->avatar_path) {
                Storage::disk('public')->delete($this->user->avatar_path);
            }

            $avatarPath = null;
        } elseif ($this->avatarUpload) {
            $avatarPath = $this->avatarUpload->store('avatars', 'public');

            if ($this->user->avatar_path) {
                Storage::disk('public')->delete($this->user->avatar_path);
            }
        }

        $this->user->update([
            'email' => $validated['email'] ?: null,
            'telephone' => $validated['telephone'] ?: null,
            'avatar_path' => $avatarPath,
        ]);

        $this->avatarUpload = null;
        $this->removeAvatar = false;
        unset($this->user);
    }

    public function clearAvatar(): void
    {
        $this->authorize('updateAvatar', $this->user);

        $this->avatarUpload = null;
        $this->removeAvatar = true;
    }

    #[Computed]
    public function record(): object
    {
        return new GetPlayerAverages($this->user, $this->currentSection)();
    }

    #[Computed]
    public function frames(): Collection
    {
        return new GetPlayerFrames($this->user, $this->currentSection)();
    }

    #[Computed]
    public function history(): Collection
    {
        return new GetPlayerSeasonHistory($this->user)();
    }

    #[Computed]
    public function knockoutMatches(): Collection
    {
        return new GetPlayerKnockoutMatches($this->user)();
    }

    public function render(): View
    {
        return view('livewire.account.show');
    }
}
