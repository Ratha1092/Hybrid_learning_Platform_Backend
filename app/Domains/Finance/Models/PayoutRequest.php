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
        'transaction_reference',
    ];

    protected $casts = [
        'details' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    // The frontend reads a flat receipt_id off a payout request (there is no
    // such column — a payout's receipt is a separate hasOne row), so this
    // must stay in sync with the receipt() relation whenever it's eager-loaded.
    protected $appends = ['receipt_id'];

    public function getReceiptIdAttribute(): ?int
    {
        return $this->receipt?->id;
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function payoutAccount()
    {
        return $this->belongsTo(InstructorPayoutAccount::class, 'payout_account_id');
    }

    public function receipt()
    {
        return $this->hasOne(PayoutReceipt::class);
    }
}