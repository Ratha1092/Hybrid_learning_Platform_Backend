<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'password' => ['required', 'string', 'max:255'],
        ];
    }
}