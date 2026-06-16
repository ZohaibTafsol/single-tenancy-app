<?php

namespace App\Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole("admin");
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ["required", "email", "unique:tenants,email"],
            'domain_name' => ["required", "unique:tenants,email"]
        ];
    }
}
