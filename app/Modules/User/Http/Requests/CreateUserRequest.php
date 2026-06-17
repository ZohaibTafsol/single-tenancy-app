<?php

namespace App\Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
return true;
    }

    public function rules(): array
    {
return [
    // TODO: define validation rules
    // 'name' => ['required', 'string', 'max:255'],
];
    }
}