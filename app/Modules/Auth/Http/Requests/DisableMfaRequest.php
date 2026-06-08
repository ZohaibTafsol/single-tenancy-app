<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisableMfaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string'],
        ];
    }
}
