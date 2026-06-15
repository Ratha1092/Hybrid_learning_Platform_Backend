<?php

namespace App\Domains\Users\Services;

use App\Domains\Notifications\Notifications\NewInstructorVerificationNotification;
use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;

class InstructorVerificationService
{
    public function apply(User $user, array $data): InstructorVerification
    {
        // Prevent duplicate applications
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
            $user->instructor_status === User::INSTRUCTOR_VERIFIED ||
            InstructorVerification::where('user_id', $user->id)
                ->where('status', 'approved')
                ->exists()
        ) {
            throw new \Exception(
                'You are already an approved instructor.'
            );
        }

        $verification = DB::transaction(function () use ($user, $data) {

            // Upload certificate
            $certificatePath = $data['certificate_file']
                ->store('verifications/certificates', 'public');

            // Upload identity document
            $identityPath = $data['identity_file']
                ->store('verifications/identities', 'public');

            // Create instructor verification
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

            $user->update([
                'instructor_status' => User::INSTRUCTOR_PENDING,
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
        $admins = User::admins()->get();

        if ($admins->isEmpty()) {
            return;
        }

        $notification = new NewInstructorVerificationNotification($verification);
        foreach ($admins as $admin) {
            $admin->notify($notification);
        }
    }
}
