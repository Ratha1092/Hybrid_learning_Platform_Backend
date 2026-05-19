<?php

namespace App\Domains\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Users\Models\User;

class OAuthAccount extends Model
{
    protected $table = 'oauth_accounts';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'email',
        'name',
        'avatar',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}