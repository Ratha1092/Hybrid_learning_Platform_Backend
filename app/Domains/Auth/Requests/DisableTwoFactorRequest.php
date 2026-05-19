<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisableTwoFactorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'password' => trim($this->password),
        ]);
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string'],
        ];
    }
}