<?php

namespace App\Modules\Payer\Services;

use App\Modules\Payer\Constants\PayerConstants;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PayerCsvRowValidator
{
    // ── US States ─────────────────────────────────────────────────────────────
    private const US_STATES = [
        'AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA','HI','ID','IL','IN',
        'IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV',
        'NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN',
        'TX','UT','VT','VA','WA','WV','WI','WY','DC','PR','GU','VI','AS','MP',
    ];

    /**
     * Validate a single CSV row (already cast to associative array).
     *
     * @param  array  $row   Associative array keyed by CSV column name
     * @param  int    $rowNo 1-based row number (for error reporting)
     * @return array         ['valid' => bool, 'errors' => string[]]
     */
    public function validate(array $row, int $rowNo): array
    {
        // Normalise booleans and case
        $row = $this->normalise($row);

        $isIndividual = ($row['file_type'] ?? '') === 'Individual';

        $rules = [
            'file_type'    => ['required', Rule::in(PayerConstants::FILE_TYPES)],

            // Individual name fields
            'first_name'   => [$isIndividual ? 'required' : 'nullable', 'string', 'max:100'],
            'middle_name'  => ['nullable', 'string', 'max:100'],
            'last_name'    => ['required', 'string', 'max:100'],
            'suffix'       => ['nullable', Rule::in(PayerConstants::SUFFIXES)],

            // ID
            'id_type'      => ['required', Rule::in(PayerConstants::ID_TYPES)],
            'id_number'    => [
                'required',
                'string',
                $isIndividual
                    ? 'regex:/^\d{3}-\d{2}-\d{4}$/'          // SSN  ###-##-####
                    : 'regex:/^\d{2}-\d{7}$/',                // EIN  ##-#######
            ],

            // Contact
            'email'        => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'regex:/^[\+\d\s\-\(\)]{7,20}$/'],

            // Address
            'address_one'  => ['required', 'string', 'max:255'],
            'address_two'  => ['nullable', 'string', 'max:255'],
            'city'         => ['required', 'string', 'max:100'],
            'state'        => [
                Rule::requiredIf(! ($row['is_foreign_address'] ?? false)),
                'nullable',
                Rule::in(self::US_STATES),
            ],
            'zip_code'     => [
                Rule::requiredIf(! ($row['is_foreign_address'] ?? false)),
                'nullable',
                'regex:/^\d{5}(-\d{4})?$/',
            ],
            'country'      => ['required', 'string', 'size:2'],
            'is_foreign_address' => ['nullable', 'boolean'],

            // Optional
            'withholding_tax_state_id' => ['nullable', 'string', 'max:100'],
            'client_payer_id'          => ['nullable', 'string', 'max:100'],
            'group_id'                 => ['nullable', 'string', 'max:100'],
            'is_last_filing'           => ['nullable', 'boolean'],
            'trade_name'               => ['nullable', 'string', 'max:200'],
            'disregarded_entity'       => ['nullable', 'string', 'max:200'],
        ];

        $validator = Validator::make($row, $rules);

        if ($validator->fails()) {
            return [
                'valid'  => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        return ['valid' => true, 'errors' => []];
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function normalise(array $row): array
    {
        // Trim every value
        $row = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row);

        // Empty string → null
        $row = array_map(fn ($v) => $v === '' ? null : $v, $row);

        // Uppercase country
        if (isset($row['country'])) {
            $row['country'] = strtoupper($row['country']);
        }

        // Default country to US for domestic addresses
        if (empty($row['country']) && empty($row['is_foreign_address'])) {
            $row['country'] = 'US';
        }

        // Cast boolean-ish columns
        foreach (['is_foreign_address', 'is_last_filing', 'is_tin_check', 'un_mask_recipient_tin'] as $col) {
            if (array_key_exists($col, $row)) {
                $row[$col] = filter_var($row[$col], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
            }
        }

        return $row;
    }
}
