<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;

class PayoutRequest extends Model
{
    protected $fillable = [
        'instructor_id',
        'payout_account_id',
        'amount',
        'currency',
        'payment_method',
        'source',
        'details',
        'status',
        'requested_at',
        'processed_at',
        'processed_by',
        'rejection_reason',
    ];

    protected $casts = [
        'details' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function payoutAccount()
    {
        return $this->belongsTo(InstructorPayoutAccount::class, 'payout_account_id');
    }
}