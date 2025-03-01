<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchDependancy extends Model
{
    use HasFactory;

    public function match()
    {
        return $this->belongsTo(KnockoutMatch::class, 'knockout_match_id');
    }

    public function dependancy()
    {
        return $this->belongsTo(KnockoutMatch::class, 'depends_on_id');
    }
}
