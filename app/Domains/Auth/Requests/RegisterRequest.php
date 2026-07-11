<?php

namespace App\Domains\Auth\Requests;

use App\Domains\System\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize input BEFORE validation
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'email' => strtolower(trim($this->email)),
            'name' => preg_replace('/\s+/', ' ', trim($this->name)), // normalize spaces
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                static::passwordRule(),
            ],
        ];
    }

    public static function passwordRule(): Password
    {
        $rule = Password::min((int) Setting::get('password_min_length', 8))
            ->mixedCase()
            ->numbers();

        if (Setting::get('password_require_special_characters', true)) {
            $rule = $rule->symbols();
        }

        return $rule;
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered',
            'password.confirmed' => 'Passwords do not match',
            'name.regex' => 'Name can only contain letters and spaces',
        ];
    }
}