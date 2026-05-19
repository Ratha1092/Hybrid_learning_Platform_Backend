<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleOAuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'email' => $this->email ? strtolower(trim($this->email)) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'id_token' => ['required', 'string'],
            'provider_id' => ['required', 'string'],
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'avatar' => ['nullable', 'url'],
        ];
    }
}
