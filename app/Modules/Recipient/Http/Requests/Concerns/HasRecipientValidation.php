<?php

namespace App\Modules\Recipient\Http\Requests\Concerns;

use App\Modules\Recipient\Constants\RecipientConstants;

trait HasRecipientValidation
{
    public function messages(): array
    {
        $isIndividual = $this->input('file_type') === 'Individual';
        $tinProvided  = ! $this->boolean('tin_not_provided');

        return [
            // ── Type ──────────────────────────────────────────────────
            'file_type.required'    => 'Please select Individual or Business.',
            'file_type.in'          => 'Type must be Individual or Business.',

            // ── Name ──────────────────────────────────────────────────
            'name.required'         => 'Business name is required.',
            'first_name.required'   => 'First name is required for Individual recipients.',
            'last_name.required'    => 'Last name is required for Individual recipients.',
            'suffix.in'             => 'Suffix must be one of: ' . implode(', ', RecipientConstants::SUFFIXES),

            // ── TIN ───────────────────────────────────────────────────
            'tin_type.required'     => 'TIN type is required when a TIN is provided.',
            'tin_type.in'           => 'TIN type must be one of: ' . implode(', ', RecipientConstants::TIN_TYPES),
            'tin.required'          => $tinProvided
                ? ($isIndividual ? 'Social Security Number is required.' : 'Employer Identification Number is required.')
                : 'TIN is required when "TIN not provided" is unchecked.',
            'tin.regex'             => $isIndividual
                ? 'SSN must be in the format 123-45-6789.'
                : 'EIN must be in the format 12-3456789.',

            // ── Contact ───────────────────────────────────────────────
            'email.email'           => 'Please enter a valid email address.',
            'phone_number.regex'    => 'Please enter a valid phone number.',

            // ── Address ────────────────────────────────────────────────
            'address_one.required'  => 'Address Line 1 is required.',
            'city.required'         => 'City is required.',
            'state.required_unless' => 'State is required for US addresses.',
            'state.in'              => 'Please select a valid US state.',
            'zip_code.required'     => 'ZIP code is required for US addresses.',
            'zip_code.regex'        => 'Enter a valid ZIP code (e.g. 85001 or 85001-1234).',
            'country.required'      => 'Country is required.',
            'country.size'          => 'Country must be a 2-letter ISO code (e.g. US).',

            // ── tax1099 Sync ───────────────────────────────────────────
            'recipient_detail_id.unique' => 'This recipient detail ID is already registered.',
        ];
    }

    public function attributes(): array
    {
        return [
            'file_type'                 => 'recipient type',
            'w8_request'                => 'W-8 request',
            'w9_request'                => 'W-9 request',
            'name'                      => 'business name',
            'first_name'                => 'first name',
            'middle_name'               => 'middle name',
            'last_name'                 => 'last name',
            'suffix'                    => 'suffix',
            'tin_not_provided'          => 'TIN not provided',
            'tin_type'                  => 'TIN type',
            'tin'                       => 'TIN',
            'attention_to'              => 'attention to',
            'email'                     => 'email address',
            'phone_number'              => 'phone number',
            'address_one'               => 'address line 1',
            'address_two'               => 'address line 2',
            'city'                      => 'city',
            'state'                     => 'state',
            'zip_code'                  => 'ZIP code',
            'country'                   => 'country',
            'is_foreign_address'        => 'foreign address',
            'client_recipient_id'       => 'client recipient ID',
            'email_language'            => 'email language',
            'account_number'            => 'account number',
            'second_tin_notice'         => '2nd TIN notice',
            'fatca_filing_requirement'  => 'FATCA filing requirement',
            'is_last_filing'            => 'last filing',
            'recipient_detail_id'       => 'recipient detail ID',
            'tin_status'                => 'TIN status',
            'is_tin_check'              => 'TIN check',
            'un_mask_recipient_tin'     => 'unmask recipient TIN',
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        // Cast all boolean/checkbox fields arriving as "0"/"1"/null
        $booleanFields = [
            'tin_not_provided',
            'w8_request',
            'w9_request',
            'is_foreign_address',
            'fatca_filing_requirement',
            'is_last_filing',
            'is_tin_check',
            'un_mask_recipient_tin',
        ];

        foreach ($booleanFields as $field) {
            $data[$field] = $this->boolean($field);
        }

        // Auto-build composite `name` for Individual from name parts if not sent explicitly
        if ($this->input('file_type') === 'Individual' && ! $this->filled('name')) {
            $parts = array_filter([
                $this->input('first_name'),
                $this->input('middle_name'),
                $this->input('last_name'),
            ]);
            $data['name'] = $parts ? implode(' ', $parts) : null;
        }

        // Normalise country and state to uppercase before size/in rules run
        if ($this->filled('country')) {
            $data['country'] = strtoupper($this->input('country'));
        }

        if ($this->filled('state')) {
            $data['state'] = strtoupper($this->input('state'));
        }

        // Default country to US for domestic addresses
        if (! $this->filled('country') && ! $this->boolean('is_foreign_address')) {
            $data['country'] = 'US';
        }

        $this->merge($data);
    }

    private function usStates(): array
    {
        return [
            'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA',
            'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD',
            'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ',
            'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC',
            'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY',
            'DC', 'PR', 'GU', 'VI', 'AS', 'MP',
        ];
    }
}