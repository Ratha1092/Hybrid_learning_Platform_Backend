<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'instructor_id',
        'amount',
        'type',
        'status',
        'revenue_share_id',
        'payout_request_id',
        'description',
    ];
}