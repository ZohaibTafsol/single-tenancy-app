<?php

namespace App\Modules\Payer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('payers.create');
    }

    public function rules(): array
    {
        $isIndividual = $this->input('file_type') === 'Individual';
        $isBusiness   = $this->input('file_type') === 'Business';
        $isForeign    = $this->boolean('is_foreign_address');

        return [
            // ── Type ──────────────────────────────────────────────────
            'file_type' => ['required', Rule::in(['Individual', 'Business'])],

            // ── Individual fields ─────────────────────────────────────
            'first_name'  => [$isIndividual ? 'nullable' : 'prohibited', 'string', 'max:100'],
            'middle_name' => [$isIndividual ? 'nullable' : 'prohibited', 'string', 'max:100'],
            'last_name'   => [$isIndividual ? 'required' : 'prohibited', 'string', 'max:100'],
            'suffix'      => [
                $isIndividual ? 'nullable' : 'prohibited',
                'string',
                'max:20',
                Rule::in(['Jr', 'Sr', 'I', 'II', 'III', 'IV', 'V', 'Esq', 'Md', 'Phd'])
            ],
            'ssn'         => [
                $isIndividual ? 'required' : 'prohibited',
                'string',
                'regex:/^\d{3}-\d{2}-\d{4}$/',   // format: 123-45-6789
            ],

            // ── Business fields ───────────────────────────────────────
            'business_name' => [$isBusiness ? 'required' : 'prohibited', 'string', 'max:200'],
            'ein'           => [
                $isBusiness ? 'required' : 'prohibited',
                'string',
                'regex:/^\d{2}-\d{7}$/',          // format: 12-3456789
            ],

            // ── Shared basic info ─────────────────────────────────────
            'email'               => ['nullable', 'email:rfc,dns', 'max:255'],
            'phone_number'        => ['required', 'string', 'max:20', 'regex:/^\+?[\d\s\-().]{7,20}$/'],
            'disregarded_entity'  => ['nullable', 'string', 'max:200'],

            // ── Address ───────────────────────────────────────────────
            'address_one'        => ['required', 'string', 'max:255'],
            'address_two'        => ['nullable', 'string', 'max:255'],
            'city'               => ['required', 'string', 'max:100'],
            'state'              => [
                'required_unless:is_foreign_address,true',
                'nullable',
                'string',
                // only enforce 2-char for US addresses
                $isForeign ? 'max:100' : Rule::in($this->usStates()),
            ],
            'zip_code'           => [
                'required',
                'string',
                'max:10',
                $isForeign ? 'regex:/^[A-Z0-9\s\-]{3,10}$/i' : 'regex:/^\d{5}(-\d{4})?$/',
            ],
            'country'            => ['required', 'string', 'size:2', 'uppercase'],
            'is_foreign_address' => ['boolean'],

            // ── Optional Information ──────────────────────────────────
            'withholding_tax_state_id' => ['nullable', 'string', 'max:100'],
            'client_payer_id'          => ['nullable', 'string', 'max:100'],
            'group_id'                 => ['nullable', 'string', 'max:100'],
            'is_last_filing'           => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'file_type.required'        => 'Please select Individual or Business.',
            'file_type.in'              => 'Type must be Individual or Business.',

            'last_name.required'        => 'Last name is required for Individual payers.',
            'ssn.required'              => 'Social Security Number is required for Individual payers.',
            'ssn.regex'                 => 'SSN must be in the format 123-45-6789.',

            'business_name.required'    => 'Business name is required for Business payers.',
            'ein.required'              => 'Employer Identification Number is required for Business payers.',
            'ein.regex'                 => 'EIN must be in the format 12-3456789.',

            'phone_number.required'     => 'Phone number is required.',
            'phone_number.regex'        => 'Please enter a valid phone number.',

            'address_one.required'      => 'Address Line 1 is required.',
            'city.required'             => 'City is required.',
            'state.required_unless'     => 'State is required for US addresses.',
            'state.in'                  => 'Please select a valid US state.',
            'zip_code.required'         => 'Zip code is required.',
            'zip_code.regex'            => 'Enter a valid zip code (e.g. 85001 or 85001-1234).',
            'country.required'          => 'Country is required.',
            'country.size'              => 'Country must be a 2-letter ISO code (e.g. US).',
        ];
    }

    public function attributes(): array
    {
        return [
            'file_type'                => 'payer type',
            'first_name'               => 'first name',
            'middle_name'              => 'middle name',
            'last_name'                => 'last name',
            'ssn'                      => 'Social Security Number',
            'business_name'            => 'business name',
            'ein'                      => 'Employer Identification Number',
            'email'                    => 'email address',
            'phone_number'             => 'phone number',
            'disregarded_entity'       => 'disregarded entity',
            'address_one'              => 'address line 1',
            'address_two'              => 'address line 2',
            'zip_code'                 => 'zip code',
            'is_foreign_address'       => 'foreign address',
            'withholding_tax_state_id' => 'withholding/tax state ID',
            'client_payer_id'          => 'client payer ID',
            'group_id'                 => 'group ID',
            'is_last_filing'           => 'last filing',
        ];
    }

    /**
     * Strip formatting from SSN/EIN before validation so the controller
     * always receives clean values regardless of what the user typed.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->filled('country')) {
            $data['country'] = strtoupper($this->input('country'));
        }

        $this->merge($data);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function usStates(): array
    {
        return [
            'AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA',
            'HI','ID','IL','IN','IA','KS','KY','LA','ME','MD',
            'MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ',
            'NM','NY','NC','ND','OH','OK','OR','PA','RI','SC',
            'SD','TN','TX','UT','VT','VA','WA','WV','WI','WY',
            'DC','PR','GU','VI','AS','MP',
        ];
    }

}
