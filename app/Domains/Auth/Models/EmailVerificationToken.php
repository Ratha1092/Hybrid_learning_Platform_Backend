<?php

namespace App\Domains\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Users\Models\User;

class EmailVerificationToken extends Model
{
    protected $table = 'email_verification_tokens';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'used',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
