<?php

namespace App\Domains\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'bio' => ['required','string','min:20','max:2000',],
            'experience' => ['required','string','min:20','max:5000',],
            'qualification_type' => ['required','in:degree,certification,professional_experience',],
            'institution' => ['required','string','max:255',],
            'completion_year' => ['required','integer','min:1950','max:' . now()->year,],
            'portfolio_url' => ['nullable','url','max:500',],
            'certificate_file' => ['required','file','mimes:pdf,jpg,jpeg,png','mimetypes:application/pdf,image/jpeg,image/png','max:10240',],
            'identity_id'   => ['required','string','max:100','unique:instructor_verifications,identity_id',],
            'identity_file' => ['required','file','mimes:pdf,jpg,jpeg,png','mimetypes:application/pdf,image/jpeg,image/png','max:10240',],
            'account_name' => ['required', 'string', 'max:255'],
            'qr_code'      => ['required', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'bio.min' =>'Your professional bio must be at least 20 characters.',
            'experience.min' =>'Please provide more details about your experience.',
            'completion_year.max' =>'Completion year cannot be in the future.',
            'certificate_file.max' =>'Certificate file size must not exceed 10MB.',
            'certificate_file.mimetypes' =>'Certificate file must be a PDF, JPG, JPEG, or PNG file.',
            'identity_id.required'  => 'Identity ID number is required.',
            'identity_id.unique'    => 'This identity ID number has already been used in another application.',
            'identity_file.max' =>'Identity file size must not exceed 10MB.',
            'identity_file.mimetypes' =>'Identity file must be a PDF, JPG, JPEG, or PNG file.',
            'account_name.required' => 'Bank account name is required.',
            'qr_code.required' => 'Please upload your payment QR code.',
            'qr_code.max' => 'QR code image must not exceed 5MB.',
            'qr_code.mimetypes' => 'QR code must be a JPG or PNG image.',
        ];
    }
}
