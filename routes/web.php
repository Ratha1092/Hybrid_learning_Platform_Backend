<?php

use App\Domains\Courses\Models\Course;
use App\Domains\Notifications\Notifications\CourseApprovedNotification;
use App\Domains\Notifications\Notifications\CourseRejectedNotification;
use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Services\BakongKhqrService;
use App\Domains\System\Models\Setting;
use Illuminate\Support\Facades\Route;

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

    Route::post('/admin/settings/{group}', function (string $group, \Illuminate\Http\Request $request) {
        if (!array_key_exists($group, \App\Filament\Pages\Settings::GROUPS)) {
            abort(404);
        }

        [$label, , , , $permission] = \App\Filament\Pages\Settings::GROUPS[$group];
        $redirectUrl = route('filament.admin.pages.settings') . '#' . $group;

        if (!auth()->user()->can("{$permission}.update")) {
            abort(403);
        }

        $settings = Setting::byGroup($group);
        $oldState = [];
        $newState = [];

        foreach ($settings as $setting) {
            $oldState[$setting->key] = $setting->value;

            if ($setting->type === 'boolean') {
                $value = $request->boolean($setting->key) ? 'true' : 'false';
            } else {
                $value = trim((string) $request->input($setting->key, $setting->value));

                if (in_array($setting->type, ['integer', 'decimal'], true) && $value !== '' && !is_numeric($value)) {
                    return redirect()->to($redirectUrl)->with('settings_error', "\"{$setting->key}\" must be a number.");
                }

                if (str_contains($setting->key, 'percentage') && is_numeric($value) && ((float) $value < 0 || (float) $value > 100)) {
                    return redirect()->to($redirectUrl)->with('settings_error', "\"{$setting->key}\" must be between 0 and 100.");
                }

                if ($setting->type === 'decimal' && is_numeric($value)) {
                    $value = (string) round((float) $value, 2);
                }
            }

            Setting::set($setting->key, $value, $group);
            $newState[$setting->key] = $value;
        }

        \App\Domains\Auth\Services\ActivityLogService::logChange("settings.{$group}_updated", $settings->first(), $oldState, $newState);

        if ($group === 'finance' && $request->boolean('apply_to_all_courses')) {
            $commission = (float) ($newState['default_commission_percentage'] ?? Setting::get('default_commission_percentage', 20));
            Course::withoutGlobalScopes()->update(['commission_percentage' => $commission]);
            $count = Course::withoutGlobalScopes()->count();
            return redirect()->to($redirectUrl)->with('settings_success', "{$label} settings updated and commission applied to all {$count} courses.");
        }

        return redirect()->to($redirectUrl)->with('settings_success', "{$label} settings updated successfully.");
    })->name('admin.settings.update');

    Route::post('/admin/roles/{role}/update', function (\Spatie\Permission\Models\Role $role, \Illuminate\Http\Request $request) {
        if (!auth()->user()->can('roles.update')) {
            abort(403);
        }

        $isProtected = in_array($role->name, \App\Filament\Resources\Roles\RoleResource::protectedRoleNames(), true);

        $oldState = [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->sort()->values()->all(),
        ];

        if (!$isProtected) {
            $name = trim((string) $request->input('name'));

            if ($name === '') {
                return back()->with('role_error', 'Role name is required.');
            }

            if (\Spatie\Permission\Models\Role::where('name', $name)->where('id', '!=', $role->id)->exists()) {
                return back()->with('role_error', "A role named \"{$name}\" already exists.");
            }

            $role->update(['name' => $name]);
        }

        $permissionIds = array_map('intval', $request->input('permissions', []));
        $permissionNames = \Spatie\Permission\Models\Permission::whereIn('id', $permissionIds)->pluck('name')->all();
        $role->syncPermissions($permissionNames);

        $newState = [
            'name' => $role->fresh()->name,
            'permissions' => $role->permissions()->pluck('name')->sort()->values()->all(),
        ];

        \App\Domains\Auth\Services\ActivityLogService::logChange('role.updated', $role, $oldState, $newState);

        return redirect()->to(url('/admin/roles/' . $role->id))->with('role_success', 'Role updated successfully.');
    })->name('admin.roles.update');

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
