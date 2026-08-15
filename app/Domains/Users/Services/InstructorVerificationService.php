<?php

namespace App\Domains\Users\Services;

use App\Domains\Notifications\Notifications\InstructorApprovedNotification;
use App\Domains\Notifications\Notifications\NewInstructorVerificationNotification;
use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\InstructorProfile;
use App\Jobs\Notifications\NotifyAdminsJob;
use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Users\Models\User;
use App\Domains\Finance\Services\PayoutAccountService;
use Illuminate\Support\Facades\DB;

class InstructorVerificationService
{
    public function __construct(
        private PayoutAccountService $payoutAccountService
    ) {
    }

    public function apply(User $user, array $data): InstructorVerification
    {
        if (
            InstructorVerification::where('user_id', $user->id)
                ->where('status', 'pending')
                ->exists()
        ) {
            throw new \Exception(
                'You already have a pending application.'
            );
        }

        if (
            InstructorVerification::where('user_id', $user->id)
                ->where('status', 'approved')
                ->exists()
        ) {
            throw new \Exception(
                'You are already an approved instructor.'
            );
        }

        $autoApprove = !Setting::get('require_instructor_verification', true)
            || Setting::get('auto_approve_instructors', false);

        $verification = DB::transaction(function () use ($user, $data, $autoApprove) {
            $certificatePath = $data['certificate_file']
                ->store('verifications/certificates', 'r2-private');
            $identityPath = $data['identity_file']
                ->store('verifications/identities', 'r2-private');
            $verification = InstructorVerification::create([
                'user_id' => $user->id,
                'bio' => $data['bio'],
                'experience' => $data['experience'],
                'qualification_type' => $data['qualification_type'],
                'institution' => $data['institution'],
                'completion_year' => $data['completion_year'],
                'portfolio_url' => $data['portfolio_url'] ?? null,
                'certificate_file' => $certificatePath,
                'identity_file' => $identityPath,
                'identity_id' => $data['identity_id'],
                'status' => $autoApprove ? 'approved' : 'pending',
                'reviewed_at' => $autoApprove ? now() : null,
            ]);

            if ($autoApprove) {
                InstructorProfile::firstOrCreate(['user_id' => $user->id]);
                $user->syncRoles(['instructor']);
            }

            $this->payoutAccountService->save($user, [
                'account_name' => $data['account_name'],
                'qr_code'      => $data['qr_code'],
            ]);

            return $verification;
        });

        if ($autoApprove) {
            $user->notify(new InstructorApprovedNotification());
        } else {
            $this->notifyAdmins($verification, $user);
        }

        return $verification;
    }

    public function latestForUser(User $user): ?InstructorVerification
    {
        return InstructorVerification::where('user_id', $user->id)
            ->latest()
            ->first();
    }

    private function notifyAdmins(
        InstructorVerification $verification,
        User $user
    ): void {
        NotifyAdminsJob::dispatch(
            NewInstructorVerificationNotification::class,
            [$verification->id, $user->name],
            'instructor_verifications.approve'
        );
    }
}
