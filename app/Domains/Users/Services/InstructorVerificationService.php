<?php

namespace App\Domains\Users\Services;

use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;

class InstructorVerificationService
{
    public function apply(User $user, array $data): InstructorVerification
    {
        // Prevent duplicate applications
        $existingApplication = InstructorVerification::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingApplication) {
            throw new \Exception(
                'You already have an active instructor application.'
            );
        }

        return DB::transaction(function () use ($user, $data) {

            // Upload certificate
            $certificatePath = $data['certificate_file']
                ->store('verifications/certificates', 'public');

            // Upload identity document
            $identityPath = $data['identity_file']
                ->store('verifications/identities', 'public');

            // Create instructor verification
            return InstructorVerification::create([
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
        });
    }
}