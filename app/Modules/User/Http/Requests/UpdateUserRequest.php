<?php

namespace App\Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
return true;
    }

    public function rules(): array
    {
return [
    // TODO: define validation rules
    // 'name' => ['sometimes', 'string', 'max:255'],
];
    }
}