<?php

namespace App\Domains\Finance\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavePayoutAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'account_name' => ['required', 'string', 'max:255'],
            'qr_code' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'qr_code.max' => 'QR code image must not exceed 5MB.',
            'qr_code.mimetypes' => 'QR code must be a JPG or PNG image.',
        ];
    }
}
