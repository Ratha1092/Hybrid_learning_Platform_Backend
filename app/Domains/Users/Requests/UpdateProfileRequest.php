<?php

namespace App\Domains\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => trim($this->name),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'expertise' => ['nullable', 'string'],
        ];
    }
}