<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'dates',
        'is_open',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dates' => 'array',
        'is_open' => 'boolean',
    ];

    public function isOpen(): bool
    {
        return $this->is_open;
    }

    /**
     * Get the sections for the season.
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get the knockouts for the season.
     */
    public function knockouts()
    {
        return $this->hasMany(Knockout::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the expulsions for the season.
     */
    public function expulsions()
    {
        return $this->hasMany(Expulsion::class);
    }

}
