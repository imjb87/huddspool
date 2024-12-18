<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
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
        'password',
        'team_id',
        'role',
        'telephone',
        'is_admin'
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

    public function getRedirectRoute()
    {
        return $this->team_id ? route('team.show', $this->team_id) : route('home');
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

    public function framesPlayed()
    {
        return Frame::where(function ($query) {
            $query->where('home_player_id', $this->id)
                ->orWhere('away_player_id', $this->id);
        })->whereHas('result.fixture.season', function ($query) {
            $query->where('is_open', true);
        })->count();
    }

    public function expulsions()
    {
        return $this->morphMany(Expulsion::class, 'expellable');
    }    
}
