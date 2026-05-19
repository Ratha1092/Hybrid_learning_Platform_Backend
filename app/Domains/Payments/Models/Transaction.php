<?php

namespace App\Domains\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'payment_id',
        'gateway',
        'event_type',
        'status',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
