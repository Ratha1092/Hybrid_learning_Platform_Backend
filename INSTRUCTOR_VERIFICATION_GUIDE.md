# Instructor Verification System - Implementation Guide

## Overview
A complete instructor verification workflow where new instructors register with evidence of qualifications and must be approved by admins before they can create courses.

## System Components

### 1. User Registration (Student-Facing)
**File**: `resources/views/auth/register.blade.php`

When users select "Instructor" during registration, they must provide:
- **Professional Bio** - Textarea describing teaching background
- **Teaching Experience** - Detailed experience description
- **Qualification Type** - Dropdown (Degree/Certification/Professional Experience)
- **Institution** - University or organization name
- **Completion Year** - Year they completed qualification
- **Portfolio URL** - Optional link to portfolio/website
- **Certificate Upload** - PDF/JPG/PNG (max 5MB)
- **ID Proof Upload** - PDF/JPG/PNG (max 5MB)

**Features**:
- Form fields show/hide based on role selection
- Drag-and-drop file uploads with visual feedback
- Form validation on client and server side
- Clear messaging about verification process

### 2. Database Schema
**File**: `database/migrations/2026_04_23_create_instructor_verifications_table.php`

#### Users Table Changes
```sql
ALTER TABLE users ADD COLUMN instructor_status VARCHAR(255) 
  -- Values: not_instructor, pending, verified, rejected
```

#### Instructor Verifications Table
```sql
CREATE TABLE instructor_verifications (
  - id (Primary Key)
  - user_id (FK to users)
  - bio (text)
  - experience (text)
  - qualification_type (enum: degree, certification, professional_experience)
  - institution (string)
  - completion_year (year)
  - certificate_file (string - file path)
  - identity_file (string - file path)
  - portfolio_url (string)
  - status (enum: pending, approved, rejected)
  - rejection_reason (text)
  - reviewed_by (FK to users - admin)
  - reviewed_at (timestamp)
  - created_at, updated_at
)
```

### 3. Models

#### InstructorVerification Model
**File**: `app/Domains/Users/Models/InstructorVerification.php`

```php
// Relationships
$verification->user()          // Applicant
$verification->reviewer()      // Admin who reviewed

// Helper Methods
$verification->isApproved()
$verification->isPending()
$verification->isRejected()

// Query Scopes
InstructorVerification::pending()
InstructorVerification::approved()
InstructorVerification::rejected()
```

#### User Model Enhancements
**File**: `app/Domains/Users/Models/User.php`

```php
// New Relationship
$user->instructorVerification()

// Helper Methods
$user->isVerifiedInstructor()       // Check if verified
$user->hasVerificationPending()     // Check if pending
$user->hasVerificationRejected()    // Check if rejected
$user->canCreateCourses()           // Business logic guard
```

### 4. Registration Controller
**File**: `app/Http/Controllers/Web/AuthController.php`

**Flow**:
1. Validate registration data (including instructor-specific fields)
2. Create user with `instructor_status = 'pending'` (if instructor role)
3. Create InstructorVerification record with submitted evidence
4. Handle file uploads to `storage/app/public/instructor-verification/`
5. Redirect to instructor dashboard
6. User sees pending notification

### 5. Admin Panel (Filament)
**Files**: 
- `app/Filament/Resources/InstructorVerificationResource.php`
- Pages in `InstructorVerificationResource/Pages/`

**Admin Features**:
- **List View**: 
  - Shows all applications with status badges
  - Filterable by status (Pending/Approved/Rejected)
  - Search by instructor name or email
  - Sorts by application date
  
- **Detail View**:
  - Displays all applicant information
  - Shows uploaded documents as images
  - Links to portfolio
  - Application timeline
  
- **Edit View**:
  - Change verification status
  - Add rejection reason if rejecting
  - Trigger approval/rejection actions
  
- **Actions**:
  - **Approve**: 
    - Sets status to 'approved'
    - Updates user's instructor_status to 'verified'
    - Records reviewer ID and timestamp
    - Sends notification (optional)
    
  - **Reject**:
    - Shows form to enter rejection reason
    - Sets status to 'rejected'
    - Updates user's instructor_status to 'rejected'
    - Stores rejection reason for applicant to see

**Navigation**: 
- Admin Dashboard → User Management → Instructor Verifications
- Shows pending count in navigation

### 6. Instructor Dashboard
**File**: `resources/views/instructor/dashboard.blade.php`

**Status Alerts** (displayed at top):

**Pending State**:
```
⏳ Verification Pending
Your instructor verification is being reviewed by our admin team. 
You'll receive an email when approved. In the meantime, you cannot create courses.
```

**Rejected State**:
```
✗ Verification Rejected
Your instructor verification was rejected. Reason: [reason text]
```

**Verified State**:
```
✓ Verified Instructor
You are verified! You can now create and publish courses.
```

### 7. Middleware Guard
**File**: `app/Http/Middleware/VerifiedInstructor.php`

Prevents unverified instructors from accessing course creation:
```php
// Redirect if not verified
if (!$user->isVerifiedInstructor()) {
    return redirect()->route('instructor.dashboard')
        ->with('error', 'You need to be verified to create courses.');
}
```

**Usage**: Apply to course creation routes
```php
Route::post('/instructor/courses', [...])
    ->middleware('verified_instructor');
```

## File Storage

Uploaded files are stored in:
- `/storage/app/public/instructor-verification/certificates/` - Certificate files
- `/storage/app/public/instructor-verification/identity/` - ID proof files

**Note**: Run `php artisan storage:link` to make files publicly accessible

## User Workflows

### New Instructor Registration
1. Visit register page
2. Select "Instructor" role
3. Fill in basic info (name, email, password)
4. Fill in professional details
5. Upload certificate and ID proof
6. Submit form
7. → See dashboard with "Pending Verification" alert
8. → Cannot create courses yet

### Admin Review Process
1. Go to Admin Panel
2. Navigate to "Instructor Verifications"
3. See pending applications
4. Click to view full details
5. Review documents and information
6. Choose to approve or reject
7. If rejecting, add reason
8. Save
9. → User receives notification
10. → User sees updated status on dashboard

### Verified Instructor
1. Admin approves verification
2. Instructor status changes to "verified"
3. Dashboard shows success message
4. Can now create and publish courses
5. Restrictions lifted

### Rejected Instructor
1. Can see rejection reason on dashboard
2. Can update profile/reapply (future feature)
3. Cannot create courses until approved

## Configuration

### File Upload Validation
- Accepted formats: PDF, JPG, JPEG, PNG
- Max file size: 5MB
- Server-side validation in controller

### Qualification Types
```php
$qualificationTypes = [
    'degree' => "Bachelor's/Master's Degree",
    'certification' => 'Professional Certification',
    'professional_experience' => 'Professional Experience',
];
```

### Status Values
```php
$statuses = [
    'not_instructor' => 'Not an instructor',
    'pending' => 'Awaiting admin review',
    'verified' => 'Approved by admin',
    'rejected' => 'Rejected by admin',
];
```

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Create Storage Link (if needed)
```bash
php artisan storage:link
```

### 3. Update Routes (Optional)
To protect course creation:
```php
Route::post('/instructor/courses', [CourseController::class, 'store'])
    ->middleware('verified_instructor');
```

### 4. Register Middleware (if not auto-discovered)
In `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    ...
    'verified_instructor' => \App\Http\Middleware\VerifiedInstructor::class,
];
```

## Email Notifications (Optional Enhancement)

Consider adding email notifications:
```php
// On approval
Mail::send(new InstructorApprovedMail($user));

// On rejection
Mail::send(new InstructorRejectedMail($user, $reason));
```

## Future Enhancements

1. **Reapply Feature**: Allow rejected instructors to resubmit
2. **Email Notifications**: Notify users on status changes
3. **Document Expiration**: Set verification expiry (e.g., 2 years)
4. **Bulk Actions**: Admin can approve/reject multiple at once
5. **Audit Log**: Track all verification state changes
6. **Badge System**: Display verified badge on instructor profiles
7. **Video Verification**: Add video introduction requirement
8. **Social Proof**: Link to teaching history on other platforms

## Troubleshooting

### Issue: Uploaded files not showing
**Solution**: Run `php artisan storage:link`

### Issue: File upload fails
**Solution**: Check file size limit (max 5MB) and format (.pdf, .jpg, .png)

### Issue: Admin can't see resource in Filament
**Solution**: Clear Filament cache: `php artisan filament:clear-cache`

### Issue: User can create courses when unverified
**Solution**: Apply `verified_instructor` middleware to course creation routes

## Testing Checklist

- [ ] Register as student (no verification fields shown)
- [ ] Register as instructor (verification fields shown)
- [ ] Upload certificate and ID proof
- [ ] Submit registration
- [ ] Instructor sees "Pending" alert on dashboard
- [ ] Admin can see application in Filament
- [ ] Admin can approve application
- [ ] Instructor sees "Verified" alert
- [ ] Admin can reject application with reason
- [ ] Instructor sees rejection message
- [ ] Unverified instructor can't create courses (if middleware applied)
