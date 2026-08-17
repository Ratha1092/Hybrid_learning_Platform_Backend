<?php

namespace App\Domains\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    private const ROLE_PRIORITY = ['super-admin', 'finance', 'instructor', 'student'];

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => collect(self::ROLE_PRIORITY)->first(fn ($r) => $this->hasRole($r)) ?? 'student',
            'instructor_status' => $this->instructorVerification?->status,
            'avatar_url' => $this->avatar_url,
            'email_verified_at' => $this->email_verified_at
                ? $this->email_verified_at->toIso8601String()
                : null,
            'created_at' => $this->created_at
                ? $this->created_at->toIso8601String()
                : null,
        ];
    }
}