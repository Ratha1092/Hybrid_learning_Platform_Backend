<?php

namespace App\Domains\Users\Services;

use App\Domains\Users\Models\User;

class UserService
{
    public function getProfile(User $user): User
    {
        return $user->load([
            'studentProfile',
            'instructorProfile'
        ]);
    }

    public function updateProfile(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'] ?? $user->name,
        ]);

        // Update student profile
        if ($user->role === 'student') {
            $user->studentProfile()->updateOrCreate([], [
                'bio' => $data['bio'] ?? null,
                'phone' => $data['phone'] ?? null,
                'country' => $data['country'] ?? null,
            ]);
        }

        // Update instructor profile
        if ($user->role === 'instructor') {
            $user->instructorProfile()->updateOrCreate([], [
                'bio' => $data['bio'] ?? null,
                'expertise' => $data['expertise'] ?? null,
            ]);
        }

        return $this->getProfile($user);
    }
}