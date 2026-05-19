<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public endpoint is fine
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'email' => strtolower(trim($this->email)),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }
}