<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GptActionAudit extends Model
{
    protected $fillable = [
        'administrator_id',
        'action',
        'subject_type',
        'subject_id',
        'before',
        'after',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
    ];
}
