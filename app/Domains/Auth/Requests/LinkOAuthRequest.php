<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkOAuthRequest extends FormRequest
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
            'provider' => ['required', 'in:google,github'],
            'provider_id' => ['required', 'string'],
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'avatar' => ['nullable', 'url'],
        ];
    }
}
