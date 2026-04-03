<?php

namespace App\Models;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Enums\UserRole;
use App\Support\PercentageFormatter;
use App\Support\SiteAuthorization;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'telephone',
        'avatar_path',
        'password',
        'role',
        'team_id',
        'is_admin',
        'email_verified_at',
        'push_prompted_at',
        'invitation_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'push_prompted_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'confirmed',
        'avatar_url',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $user): void {
            if ($user->wasRecentlyCreated || $user->wasChanged(['role', 'is_admin'])) {
                SiteAuthorization::syncSpatieRoleFromLegacyColumns($user);
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can(PermissionName::AccessAdminPanel->value);
    }

    public function getRedirectRoute(): string
    {
        return route('account.show');
    }

    /**
     * Check if the user has confirmed their email address.
     */
    public function getConfirmedAttribute(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Get the user's team.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->whereNull('deleted_at');
    }

    public function unreadNotifications(): MorphMany
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function frames(): HasMany
    {
        return $this->hasMany(Frame::class, 'home_player_id')
            ->whereHas('result.fixture.season', function ($query) {
                $query->where('is_open', true);
            })
            ->orWhere('away_player_id', $this->id)
            ->whereHas('result.fixture.season', function ($query) {
                $query->where('is_open', true);
            });
    }

    public function framesWon(): HasMany
    {
        return $this->hasMany(Frame::class, 'home_player_id')
            ->where(function ($query) {
                $query->whereColumn('home_score', '>', 'away_score')
                    ->whereHas('result.fixture.season', function ($query) {
                        $query->where('is_open', true);
                    });
            })
            ->orWhere(function ($query) {
                $query->whereColumn('away_score', '>', 'home_score')
                    ->where('away_player_id', $this->id)
                    ->whereHas('result.fixture.season', function ($query) {
                        $query->where('is_open', true);
                    });
            });
    }

    public function framesLost(): HasMany
    {
        return $this->hasMany(Frame::class, 'home_player_id')
            ->where(function ($query) {
                $query->whereColumn('home_score', '<', 'away_score')
                    ->whereHas('result.fixture.season', function ($query) {
                        $query->where('is_open', true);
                    });
            })
            ->orWhere(function ($query) {
                $query->whereColumn('away_score', '<', 'home_score')
                    ->where('away_player_id', $this->id)
                    ->whereHas('result.fixture.season', function ($query) {
                        $query->where('is_open', true);
                    });
            });
    }

    public function winPercentage(): float|int|string
    {
        $framesPlayed = $this->frames->count();

        if ($framesPlayed === 0) {
            return 0;
        }

        return PercentageFormatter::trimmedSingleDecimal(($this->framesWon()->count() / $framesPlayed) * 100);
    }

    public function lossPercentage(): float|int|string
    {
        $framesPlayed = $this->frames->count();

        if ($framesPlayed === 0) {
            return 0;
        }

        return PercentageFormatter::trimmedSingleDecimal(($this->framesLost()->count() / $framesPlayed) * 100);
    }

    public function expulsions(): MorphMany
    {
        return $this->morphMany(Expulsion::class, 'expellable');
    }

    public function isTeamAdmin(): bool
    {
        return $this->hasRole(RoleName::TeamAdmin->value)
            || $this->hasRole(RoleName::Admin->value);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleName::Admin->value);
    }

    public function isCaptain(): bool
    {
        return $this->id === $this->team?->captain_id;
    }

    public function roleLabel(): string
    {
        if ($this->isAdmin()) {
            return 'Admin';
        }

        return $this->roleEnum()?->label() ?? UserRole::Player->label();
    }

    public function roleEnum(): ?UserRole
    {
        if ($this->role === null) {
            return null;
        }

        return UserRole::tryFrom((string) $this->role);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
            return Storage::url($this->avatar_path);
        }

        return asset('/images/user.jpg');
    }
}
