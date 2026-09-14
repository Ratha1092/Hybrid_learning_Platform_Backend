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
        $this->merge(['credential' => trim((string) $this->credential)]);
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'in:google,github'],
            'credential' => ['required', 'string', 'max:10000'],
        ];
    }
}
