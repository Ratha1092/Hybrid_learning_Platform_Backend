<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyLoginTwoFactorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'challenge_token' => trim((string) $this->challenge_token),
            'code' => trim((string) $this->code),
        ]);
    }

    public function rules(): array
    {
        return [
            'challenge_token' => ['required', 'string', 'size:64'],
            'code' => ['required', 'string', 'size:6'],
        ];
    }
}
