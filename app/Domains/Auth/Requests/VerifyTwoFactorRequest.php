<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyTwoFactorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'code' => trim($this->code),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email', 'max:255'],
            'code' => ['required', 'string', 'size:6'],
        ];
    }
}