<?php

use App\Domains\Courses\Models\Course;
use App\Domains\Notifications\Notifications\CourseApprovedNotification;
use App\Domains\Notifications\Notifications\CourseRejectedNotification;
use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Services\BakongKhqrService;
use App\Domains\System\Models\Setting;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Fallback Only)
|--------------------------------------------------------------------------
| This is a minimal fallback. Main system uses API + React.
*/

Route::get('/web', function () {
    return 'Web fallback active';
});

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::middleware(['web', 'auth'])->group(function () {

    Route::post('/admin/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    })->name('admin.notifications.mark-all-read');

    Route::post('/admin/notifications/{id}/mark-read', function (string $id) {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    })->name('admin.notifications.mark-read');

    Route::post('/admin/courses/{course}/approve', function (Course $course) {
        if ($course->isPendingReview()) {
            $course->publish(auth()->id());
            $course->instructor?->notify(new CourseApprovedNotification($course));
            session()->flash('course_success', 'Course approved and published.');
        }
        return redirect()->to(url('/admin/courses/' . $course->id));
    })->name('admin.courses.approve');

    Route::post('/admin/courses/{course}/reject', function (Course $course) {
        $reason = trim(request('reason', ''));
        if ($reason === '') {
            session()->flash('course_error', 'Rejection reason is required.');
            return redirect()->back();
        }
        $course->reject($reason);
        $course->instructor?->notify(new CourseRejectedNotification($course, $reason));
        session()->flash('course_success', 'Course rejected. Instructor notified.');
        return redirect()->to(url('/admin/courses/' . $course->id));
    })->name('admin.courses.reject');

    Route::post('/admin/courses/{course}/return-to-draft', function (Course $course) {
        $course->update([
            'status'           => Course::STATUS_DRAFT,
            'is_published'     => false,
            'approved_at'      => null,
            'approved_by'      => null,
            'rejection_reason' => null,
        ]);
        session()->flash('course_success', 'Course returned to draft.');
        return redirect()->back();
    })->name('admin.courses.return-to-draft');

    Route::post('/admin/settings/commission', function (\Illuminate\Http\Request $request) {
        $value = $request->input('default_commission_percentage');

        if (!is_numeric($value) || (float)$value < 0 || (float)$value > 100) {
            return back()->with('settings_error', 'Commission must be a number between 0 and 100.');
        }

        $value = round((float) $value, 2);
        Setting::set('default_commission_percentage', $value, 'finance');

        if ($request->boolean('apply_to_all_courses')) {
            Course::withoutGlobalScopes()->update(['commission_percentage' => $value]);
            $count = Course::withoutGlobalScopes()->count();
            return back()->with('settings_success', "Commission updated to {$value}% and applied to all {$count} courses.");
        }

        return back()->with('settings_success', "Default commission set to {$value}%. New courses will use this rate.");
    })->name('admin.settings.commission');

    Route::post('/admin/courses/{course}/archive', function (Course $course) {
        if ($course->isPublished()) {
            $course->archive();
            session()->flash('course_success', 'Course archived.');
        }
        return redirect()->to(url('/admin/courses/' . $course->id));
    })->name('admin.courses.archive');

});

Route::middleware(['web', 'auth'])->get(
    '/admin/export/courses',
    function () {
        $tab    = request('tab', 'all');
        $search = request('search', '');

        $statusMap = [
            'pending'   => Course::STATUS_PENDING,
            'published' => Course::STATUS_PUBLISHED,
            'draft'     => Course::STATUS_DRAFT,
            'rejected'  => Course::STATUS_REJECTED,
            'archived'  => Course::STATUS_ARCHIVED,
        ];

        $query = Course::withoutGlobalScopes()
            ->with(['instructor:id,name', 'category:id,name'])
            ->withCount('enrollments');

        if ($tab !== 'all' && isset($statusMap[$tab])) {
            $query->where('status', $statusMap[$tab]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhereHas('instructor', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('category',   fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $rows = [['ID', 'Title', 'Instructor', 'Category', 'Price', 'Status', 'Students', 'Created']];

        foreach ($query->orderBy('id', 'desc')->get() as $course) {
            $rows[] = [
                $course->id,
                $course->title,
                $course->instructor?->name ?? '',
                $course->category?->name ?? '',
                number_format((float) $course->price, 2),
                $course->status,
                $course->enrollments_count,
                $course->created_at?->format('M d, Y') ?? '',
            ];
        }

        $csv = implode("\n", array_map(
            fn($row) => implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string) $v) . '"', $row)),
            $rows
        ));

        $filename = 'courses-' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
)->name('admin.export.courses');

Route::middleware(['web', 'auth'])->post(
    '/admin/payments/{payment}/force-verify',
    function (Payment $payment) {
        $result = app(BakongKhqrService::class)->forceVerifyPayment($payment);

        if ($result->isPaid()) {
            session()->flash('force_verify_success', 'Payment #' . $payment->id . ' marked as Paid.');
        } else {
            session()->flash('force_verify_info', 'Bakong has not confirmed this payment yet. Status: ' . ($result->status?->value ?? 'unknown'));
        }

        return redirect()->back();
    }
)->name('admin.payments.force-verify');
