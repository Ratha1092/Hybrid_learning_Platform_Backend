<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Only required when the user already has a real password to verify —
            // an OAuth-only account (has_password = false) has none, so setting
            // one for the first time skips this check entirely.
            'current_password' => [$this->user()?->has_password ? 'required' : 'sometimes', 'string'],
            'password' => [
                'required',
                'confirmed',
                RegisterRequest::passwordRule(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Your current password is required.',
            'password.confirmed' => 'Passwords do not match',
        ];
    }
}
