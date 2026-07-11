<?php

namespace App\Console\Commands;

use App\Domains\Analytics\Models\CourseView;
use App\Domains\Auth\Models\ActivityLog;
use App\Domains\System\Models\Setting;
use Illuminate\Console\Command;

/**
 * Prunes rows older than the admin-configured retention windows:
 * audit_log_retention_days (security group) for the activity log, and
 * retain_analytics_days (analytics group) for course view events.
 */
class PruneRetainedDataCommand extends Command
{
    protected $signature   = 'app:prune-retained-data';
    protected $description = 'Delete activity log and analytics rows past their configured retention window';

    public function handle(): int
    {
        $auditDays = (int) Setting::get('audit_log_retention_days', 365);
        if ($auditDays > 0) {
            $deleted = ActivityLog::where('created_at', '<', now()->subDays($auditDays))->delete();
            $this->line("Pruned {$deleted} activity log row(s) older than {$auditDays} days.");
        }

        $analyticsDays = (int) Setting::get('retain_analytics_days', 90);
        if ($analyticsDays > 0) {
            $deleted = CourseView::where('created_at', '<', now()->subDays($analyticsDays))->delete();
            $this->line("Pruned {$deleted} course view row(s) older than {$analyticsDays} days.");
        }

        return self::SUCCESS;
    }
}
