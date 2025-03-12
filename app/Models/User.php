<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'telephone',
        'password',
        'team_id',
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
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'confirmed',
    ];                            

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function getRedirectRoute()
    {
        // redirect user to cp if admin or their own profile if player
        return route($this->isAdmin() ? 'filament.admin.pages.dashboard' : 'player.show', $this);
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
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function frames()
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

    public function framesWon()
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

    public function framesLost()
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

    public function winPercentage()
    {
        $framesPlayed = $this->frames->count();

        if ($framesPlayed === 0) {
            return 0;
        }

        return number_format(($this->framesWon()->count() / $framesPlayed) * 100, 2);
    }

    public function lossPercentage()
    {
        $framesPlayed = $this->frames->count();

        if ($framesPlayed === 0) {
            return 0;
        }

        return number_format(($this->framesLost()->count() / $framesPlayed) * 100, 2);
    }

    public function expulsions()
    {
        return $this->morphMany(Expulsion::class, 'expellable');
    }

    public function isTeamAdmin()
    {
        return $this->role == 2;
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function isCaptain()
    {
        return $this->id === $this->team?->captain_id;
    }

    public function role()
    {
        if ($this->isCaptain()) {
            return 'Captain';
        }

        if ($this->isTeamAdmin()) {
            return 'Team Admin';
        }

        return 'Player';
    }
}
