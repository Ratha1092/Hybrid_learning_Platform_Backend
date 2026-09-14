<?php

namespace App\Domains\Finance\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstructorPayoutAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'instructor_id',
        'method',
        'account_name',
        'account_number',
        'phone_number',
        'qr_code_path',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'deleted_by',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    protected $appends = ['qr_code_url'];

    protected static function booted(): void
    {
        static::deleting(function (InstructorPayoutAccount $account) {
            if (auth()->check()) {
                $account->deleted_by = auth()->id();
                $account->saveQuietly();
            }
        });
    }

    public function getQrCodeUrlAttribute(): ?string
    {
        if (!$this->qr_code_path) {
            return null;
        }

        return \Storage::disk('r2')->url($this->qr_code_path);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }
}
