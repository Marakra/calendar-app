<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Event extends Model
{
    protected $fillable = [
        'id',
        'title',
        'description',
        'start_date',
        'end_date',
        'is_completed',
        'priority',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public $timestamps = true;
}
