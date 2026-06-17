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
            'domain_name' => [
                'required',
                'string',
                'alpha_dash',
                'max:63',
                function ($attribute, $value, $fail) {
                    $centralDomains = config('tenancy.central_domains') ?? ['localhost'];

                    $fullDomains = array_map(
                        fn($domain) => $value . '.' . $domain,
                        $centralDomains
                    );

                    if (\DB::table('domains')->whereIn('domain', $fullDomains)->exists()) {
                        $fail('The domain has already been taken.');
                    }
                }
            ],

        ];
    }
}
