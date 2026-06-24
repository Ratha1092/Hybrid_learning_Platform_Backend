<?php

namespace App\Domains\Users\Services;

use App\Domains\Notifications\Notifications\NewInstructorVerificationNotification;
use App\Jobs\Notifications\NotifyAdminsJob;
use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;

class InstructorVerificationService
{
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

        $verification = DB::transaction(function () use ($user, $data) {
            $certificatePath = $data['certificate_file']
                ->store('verifications/certificates', 'public');
            $identityPath = $data['identity_file']
                ->store('verifications/identities', 'public');
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
                'status' => 'pending',
            ]);

            return $verification;
        });

        $this->notifyAdmins($verification, $user);

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
            [$verification->id, $user->name]
        );
    }
}
