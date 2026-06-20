<?php

namespace App\Domains\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->getRoleNames()->first() ?? 'student',
            'instructor_status' => $this->instructorVerification?->status,
            'email_verified_at' => $this->email_verified_at
                ? $this->email_verified_at->toIso8601String()
                : null,
            'created_at' => $this->created_at
                ? $this->created_at->toIso8601String()
                : null,
        ];
    }
}