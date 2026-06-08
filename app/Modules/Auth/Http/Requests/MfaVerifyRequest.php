<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MfaVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'otp' => ['required', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required' => 'OTP code is required.',
            'otp.digits'   => 'OTP must be exactly 6 digits.',
        ];
    }
}
