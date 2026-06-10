<?php

namespace App\Modules\Payer\Http\Requests\Concerns;

use App\Modules\Payer\Constants\PayerConstants;

trait HasPayerValidation
{
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
            'suffix.in'              => 'Suffix must be in: ' . implode(', ', PayerConstants::SUFFIXES),
            'id_type'                => "ID type must be in: " . implode(', ', PayerConstants::ID_TYPES),

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
        if (! $this->filled('country') && ! $this->boolean('is_foreign_address')) {
            $data['country'] = 'US';
        }

        if ($data) {
            $this->merge($data);
        }
    }

    private function usStates(): array
    {
        return [
            'AL',
            'AK',
            'AZ',
            'AR',
            'CA',
            'CO',
            'CT',
            'DE',
            'FL',
            'GA',
            'HI',
            'ID',
            'IL',
            'IN',
            'IA',
            'KS',
            'KY',
            'LA',
            'ME',
            'MD',
            'MA',
            'MI',
            'MN',
            'MS',
            'MO',
            'MT',
            'NE',
            'NV',
            'NH',
            'NJ',
            'NM',
            'NY',
            'NC',
            'ND',
            'OH',
            'OK',
            'OR',
            'PA',
            'RI',
            'SC',
            'SD',
            'TN',
            'TX',
            'UT',
            'VT',
            'VA',
            'WA',
            'WV',
            'WI',
            'WY',
            'DC',
            'PR',
            'GU',
            'VI',
            'AS',
            'MP',
        ];
    }
}
