<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'token' => trim($this->token),
        ]);
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'max:255'],
        ];
    }
}