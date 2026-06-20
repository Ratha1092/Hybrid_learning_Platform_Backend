<?php

namespace App\Domains\Finance\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorWallet extends Model
{
    protected $fillable = [
        'instructor_id',
        'balance',
        'pending_balance',
        'currency',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}