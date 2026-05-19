<?php

namespace App\Domains\Users\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'email_verified_at' => $this->email_verified_at,

            'student_profile' => $this->whenLoaded('studentProfile'),
            'instructor_profile' => $this->whenLoaded('instructorProfile'),

            'created_at' => $this->created_at,
        ];
    }
}