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

            // ── Name ──────────────────────────────────────────────────
            // Business: required as the primary display name
            // Individual: optional composite (first+last used instead)
            'name' => [$isBusiness ? 'required' : 'nullable', 'string', 'max:100'],

            // ── Individual-only fields ────────────────────────────────
            // 'prohibited' must be the only rule when blocking a field;
            // trailing type/length rules would still fire on prohibited fields.
            'first_name'  => $isIndividual
                ? ['nullable', 'string', 'max:100']
                : ['prohibited'],
            'middle_name' => $isIndividual
                ? ['nullable', 'string', 'max:100']
                : ['prohibited'],
            'last_name'   => $isIndividual
                ? ['required', 'string', 'max:100']
                : ['prohibited'],
            'suffix'      => $isIndividual
                ? ['nullable', 'string', Rule::in(['Jr', 'Sr', '2nd', 'C3rd', 'II', 'III', 'IV', 'V', 'VI'])]
                : ['prohibited'],

            // ── ID Number (SSN for Individual, EIN for Business) ──────
            'id_type' => ['required', Rule::in(['SSN', 'EIN'])],

            // Stored in a single `id_number` column; format differs by type.
            'id_number' => [
                'required',
                'string',
                'max:11',
                $isIndividual
                    ? 'regex:/^\d{3}-\d{2}-\d{4}$/'   // SSN: 123-45-6789
                    : 'regex:/^\d{2}-\d{7}$/',          // EIN: 12-3456789
            ],

            // ── Shared basic info ─────────────────────────────────────
            'email'              => ['nullable', 'email:rfc', 'max:255'],
            'phone_number'       => ['required', 'string', 'max:20', 'regex:/^\+?[\d\s\-().]{7,20}$/'],
            'disregarded_entity' => ['nullable', 'string', 'max:200'],

            // ── Address ───────────────────────────────────────────────
            'address_one' => ['required', 'string', 'max:255'],
            'address_two' => ['nullable', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:100'],
            'state'       => [
                'required_unless:is_foreign_address,true',
                'nullable',
                'string',
                $isForeign ? 'max:100' : Rule::in($this->usStates()),
            ],
            'zip_code' => [
                'required',
                'string',
                'max:10',
                $isForeign
                    ? 'regex:/^[A-Z0-9\s\-]{3,10}$/i'
                    : 'regex:/^\d{5}(-\d{4})?$/',
            ],
            // country is uppercased in prepareForValidation before this runs
            'country'            => ['required', 'string', 'size:2'],
            'is_foreign_address' => ['boolean'],

            // ── Optional Information ──────────────────────────────────
            'withholding_tax_state_id' => ['nullable', 'string', 'max:100'],
            'client_payer_id'          => ['nullable', 'string', 'max:100'],
            'group_id'                 => ['nullable', 'string', 'max:100'],
            'is_last_filing'           => ['boolean'],

            // ── tax1099 sync fields ───────────────────────────────────
            'payer_detail_id'       => ['nullable', 'string', 'max:100', 'unique:payers,payer_detail_id'],
            'tin_status'            => ['nullable', 'string', 'max:50'],
            'is_tin_check'          => ['boolean'],
            'un_mask_recipient_tin' => ['boolean'],
            'trade_name'            => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        $isIndividual = $this->input('file_type') === 'Individual';

        return [
            'file_type.required'     => 'Please select Individual or Business.',
            'file_type.in'           => 'Type must be Individual or Business.',

            'name.required'          => 'Business name is required.',

            'last_name.required'     => 'Last name is required for Individual payers.',
            'id_type.required'       => 'ID type is required and must be either SSN or EIN.',
            'id_number.required'     => $isIndividual
                ? 'Social Security Number is required.'
                : 'Employer Identification Number is required.',
            'id_number.regex'        => $isIndividual
                ? 'SSN must be in the format 123-45-6789.'
                : 'EIN must be in the format 12-3456789.',

            'phone_number.required'  => 'Phone number is required.',
            'phone_number.regex'     => 'Please enter a valid phone number.',

            'address_one.required'   => 'Address Line 1 is required.',
            'city.required'          => 'City is required.',
            'state.required_unless'  => 'State is required for US addresses.',
            'state.in'               => 'Please select a valid US state.',
            'zip_code.required'      => 'Zip code is required.',
            'zip_code.regex'         => 'Enter a valid zip code (e.g. 85001 or 85001-1234).',
            'country.required'       => 'Country is required.',
            'country.size'           => 'Country must be a 2-letter ISO code (e.g. US).',

            'payer_detail_id.unique' => 'This payer detail ID is already registered.',
        ];
    }

    public function attributes(): array
    {
        return [
            'file_type'                => 'payer type',
            'name'                     => 'business name',
            'first_name'               => 'first name',
            'middle_name'              => 'middle name',
            'last_name'                => 'last name',
            'id_number'                => 'ID number',
            'id_type'                  => 'ID type',
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
            'payer_detail_id'          => 'payer detail ID',
            'tin_status'               => 'TIN status',
            'is_tin_check'             => 'TIN check',
            'un_mask_recipient_tin'    => 'unmask recipient TIN',
            'trade_name'               => 'trade name',
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        // Normalise country to uppercase before the size:2 rule runs
        if ($this->filled('country')) {
            $data['country'] = strtoupper($this->input('country'));
        }

        // Default country to US for domestic addresses
        if (!$this->filled('country') && !$this->boolean('is_foreign_address')) {
            $data['country'] = 'US';
        }

        if ($data) {
            $this->merge($data);
        }
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