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
            'bio' => [
                'required',
                'string',
                'min:20',
                'max:2000',
            ],

            'experience' => [
                'required',
                'string',
                'min:20',
                'max:5000',
            ],

            'qualification_type' => [
                'required',
                'in:degree,certification,professional_experience',
            ],

            'institution' => [
                'required',
                'string',
                'max:255',
            ],

            'completion_year' => [
                'required',
                'integer',
                'min:1950',
                'max:' . now()->year,
            ],

            'portfolio_url' => [
                'nullable',
                'url',
                'max:500',
            ],

            'certificate_file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],

            'identity_file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'bio.min' =>
                'Your professional bio must be at least 20 characters.',

            'experience.min' =>
                'Please provide more details about your experience.',

            'completion_year.max' =>
                'Completion year cannot be in the future.',

            'certificate_file.max' =>
                'Certificate file size must not exceed 10MB.',

            'identity_file.max' =>
                'Identity file size must not exceed 10MB.',
        ];
    }
}