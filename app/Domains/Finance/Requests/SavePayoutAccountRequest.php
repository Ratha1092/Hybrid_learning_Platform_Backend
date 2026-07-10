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
            'method' => ['required', 'string', 'in:bank_transfer,momo,acleda,wing,khqr'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required_if:method,bank_transfer', 'nullable', 'string', 'max:100'],
            'phone_number' => ['required_if:method,momo,wing', 'nullable', 'string', 'max:30'],
            'qr_code' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_number.required_if' => 'Account number is required for bank transfer.',
            'phone_number.required_if' => 'Phone number is required for this payment method.',
            'qr_code.max' => 'QR code image must not exceed 5MB.',
            'qr_code.mimetypes' => 'QR code must be a JPG or PNG image.',
        ];
    }
}
