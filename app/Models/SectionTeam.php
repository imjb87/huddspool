<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
 
class SectionTeam extends Pivot
{
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
 
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}