<?php

namespace App\Domains\Analytics\Models;

use Illuminate\Database\Eloquent\Model;

class DailyMetric extends Model
{
    protected $fillable = [
        'date',
        'total_users',
        'new_users',
        'total_orders',
        'total_revenue',
        'total_enrollments',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}