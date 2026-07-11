<?php

namespace App\Domains\Learning\Controllers;

use App\Domains\Learning\Models\ContentReport;
use App\Domains\Notifications\Notifications\NewContentReportNotification;
use App\Domains\System\Models\Setting;
use App\Http\Controllers\Controller;
use App\Jobs\Notifications\NotifyAdminsJob;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (!Setting::get('allow_user_reports', true)) {
            return ApiResponse::error('Content reporting is currently disabled.', 403);
        }

        $validated = $request->validate([
            'reportable_type' => ['required', 'string', 'in:course,review'],
            'reportable_id' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:100'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $modelClass = Relation::morphMap()[$validated['reportable_type']] ?? null;

        if (!$modelClass || !$modelClass::where('id', $validated['reportable_id'])->exists()) {
            return ApiResponse::error('The reported item was not found.', 404);
        }

        $report = ContentReport::create([
            'reporter_id' => $request->user()->id,
            'reportable_type' => $validated['reportable_type'],
            'reportable_id' => $validated['reportable_id'],
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
            'status' => 'pending',
        ]);

        NotifyAdminsJob::dispatch(
            NewContentReportNotification::class,
            [$report->id, $request->user()->name, $validated['reportable_type']]
        );

        return ApiResponse::success($report, 'Report submitted. Our team will review it shortly.', 201);
    }
}
