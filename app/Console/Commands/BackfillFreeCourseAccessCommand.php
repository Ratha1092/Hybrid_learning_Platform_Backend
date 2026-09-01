<?php

namespace App\Console\Commands;

use App\Domains\Learning\Models\Enrollment;
use Illuminate\Console\Command;

class BackfillFreeCourseAccessCommand extends Command
{
    protected $signature = 'enrollments:backfill-free-access {--dry-run : List affected enrollments without changing anything}';
    protected $description = 'Null out expires_at on enrollments for $0-priced courses that were stamped with an access-duration expiry before that was fixed';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $enrollments = Enrollment::query()
            ->whereNotNull('expires_at')
            ->whereHas('course', fn ($q) => $q->where('price', 0))
            ->with(['user:id,name,email', 'course:id,title,price'])
            ->get();

        if ($enrollments->isEmpty()) {
            $this->info('No free-course enrollments have a stale expires_at.');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info("{$enrollments->count()} enrollment(s) would be backfilled:");
            foreach ($enrollments as $enrollment) {
                $this->line(sprintf(
                    '  #%d  user=%s  course="%s"  expires_at=%s',
                    $enrollment->id,
                    $enrollment->user?->email ?? $enrollment->user_id,
                    $enrollment->course?->title ?? $enrollment->course_id,
                    $enrollment->expires_at->toDateString(),
                ));
            }
            return self::SUCCESS;
        }

        $count = $enrollments->count();
        Enrollment::query()
            ->whereNotNull('expires_at')
            ->whereHas('course', fn ($q) => $q->where('price', 0))
            ->update(['expires_at' => null]);

        $this->info("Done — {$count} enrollment(s) backfilled to permanent access.");

        return self::SUCCESS;
    }
}
