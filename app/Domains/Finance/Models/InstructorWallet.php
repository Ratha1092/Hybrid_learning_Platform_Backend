<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorWallet extends Model
{
    protected $fillable = [
        'instructor_id',
        'balance',
        'pending_balance',
        'currency',
    ];
}