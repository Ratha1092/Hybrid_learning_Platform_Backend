<?php

namespace App\Domains\Users\Services;

use App\Domains\Users\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserService
{
    private const USER_FIELDS = ['name','phone',];
    private const STUDENT_PROFILE_FIELDS = [
        'bio',
        'learning_goals',
        'interests',
        'github',
        'linkedin',
    ];

    private const INSTRUCTOR_PROFILE_FIELDS = [
        'bio',
        'website',
        'twitter',
        'linkedin',
        'youtube',
    ];

    public function getProfile(User $user): User
    {
        return $user->load([
            'studentProfile',
            'instructorProfile'
        ]);
    }

    public function updateProfile(User $user, array $data): User
    {
        $userData = $this->onlyPresent($data, self::USER_FIELDS);

        if ($userData !== []) {
            $user->update($userData);
        }
        if ($user->isStudent()) {
            $studentProfileData = $this->onlyPresent(
                $data,
                self::STUDENT_PROFILE_FIELDS
            );
            if ($studentProfileData !== []) {
                $user->studentProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $studentProfileData
                );
            }
        }
        if ($user->isInstructor()) {
            $instructorProfileData = $this->onlyPresent(
                $data,
                self::INSTRUCTOR_PROFILE_FIELDS
            );
            if ($instructorProfileData !== []) {
                $user->instructorProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $instructorProfileData
                );
            }
        }

        return $this->getProfile($user);
    }

    public function updateAvatar(User $user, UploadedFile $file): User
    {
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $file->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return $user->refresh();
    }

    private function onlyPresent(array $data, array $keys): array
    {
        return array_intersect_key($data, array_flip($keys));
    }
}
