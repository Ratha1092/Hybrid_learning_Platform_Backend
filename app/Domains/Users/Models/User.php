<?php

namespace App\Domains\Users\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Database\Factories\UserFactory;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\LessonProgress;
use App\Domains\Learning\Models\Review;
use App\Domains\Learning\Models\Wishlist;
use App\Domains\Orders\Models\Order;
use App\Domains\Analytics\Models\CourseView;
use App\Domains\Finance\Models\RevenueShare;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\PayoutRequest;
use App\Domains\Users\Models\InstructorProfile;
use App\Domains\Users\Models\StudentProfile;
use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Auth\Models\EmailVerificationToken;
use App\Domains\Auth\Models\PasswordResetToken;
use App\Domains\Auth\Models\TwoFactorCode;
use App\Domains\Auth\Models\ActivityLog;
use App\Domains\Auth\Models\OAuthAccount;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use SoftDeletes;
    use HasRoles;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const INSTRUCTOR_NONE = 'not_instructor';
    public const INSTRUCTOR_PENDING = 'pending';
    public const INSTRUCTOR_VERIFIED = 'verified';
    public const INSTRUCTOR_REJECTED = 'rejected';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'avatar',
        'phone',
        'role',
        'instructor_status',
        'status',
        'last_login_at',
        'two_factor_enabled',
        'two_factor_secret',
        'oauth_provider',
        'oauth_id',
        'oauth_avatar',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'password' => 'hashed',
    ];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

   public function enrollments()
    {
        return $this->hasMany(
            \App\Domains\Learning\Models\Enrollment::class
        );
    }

    public function lessonProgress()
    {
        return $this->hasMany(\App\Domains\Learning\Models\LessonProgress::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function courseViews(): HasMany
    {
        return $this->hasMany(CourseView::class);
    }

    public function revenueShares(): HasMany
    {
        return $this->hasMany(RevenueShare::class, 'instructor_id');
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(InstructorWallet::class, 'instructor_id');
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(PayoutRequest::class, 'instructor_id');
    }

    public function instructorProfile(): HasOne
    {
        return $this->hasOne(InstructorProfile::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function instructorVerification(): HasOne
    {
        return $this->hasOne(InstructorVerification::class);
    }

    public function emailVerificationTokens(): HasMany
    {
        return $this->hasMany(EmailVerificationToken::class);
    }

    public function passwordResetTokens(): HasMany
    {
        return $this->hasMany(PasswordResetToken::class);
    }

    public function twoFactorCodes(): HasMany
    {
        return $this->hasMany(TwoFactorCode::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function oauthAccounts(): HasMany
    {
        return $this->hasMany(OAuthAccount::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor' || $this->hasRole('instructor');
    }

    public function isStudent(): bool
    {
        return $this->role === 'student' || $this->hasRole('student');
    }

    public function isVerifiedInstructor(): bool
    {
        return ($this->role === 'instructor' || $this->hasRole('instructor'))
            && $this->instructor_status === self::INSTRUCTOR_VERIFIED;
    }

    public function hasVerificationPending(): bool
    {
        return ($this->role === 'instructor' || $this->hasRole('instructor'))
            && $this->instructor_status === self::INSTRUCTOR_PENDING;
    }

    public function hasVerificationRejected(): bool
    {
        return ($this->role === 'instructor' || $this->hasRole('instructor'))
            && $this->instructor_status === self::INSTRUCTOR_REJECTED;
    }

    public function canCreateCourses(): bool
    {
        return $this->isVerifiedInstructor();
    }

    public function scopeAdmins($query)
    {
        return $query->role('admin');
    }

    public function scopeInstructors($query)
    {
        return $query->role('instructor');
    }

    public function scopeStudents($query)
    {
        return $query->role('student');
    }
    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin'
            && $this->hasRole('admin');
    }
    public function hasEnrolledCourse(int $courseId): bool {
            return $this->enrollments()
                ->where('course_id', $courseId)
                ->where('status', 'active')
                ->exists();
    }
}