<?php

namespace App\Domains\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Users\Models\User;

class InstructorVerification extends Model
{
    use SoftDeletes;

    protected $table = 'instructor_verifications';

    protected $fillable = [
        'user_id',
        'bio',
        'experience',
        'qualification_type',
        'institution',
        'completion_year',
        'certificate_file',
        'identity_file',
        'portfolio_url',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'completion_year' => 'integer',
    ];

    /**
     * The user this verification belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin who reviewed this verification
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if verification is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if verification is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if verification is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Scope to get pending verifications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved verifications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected verifications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
