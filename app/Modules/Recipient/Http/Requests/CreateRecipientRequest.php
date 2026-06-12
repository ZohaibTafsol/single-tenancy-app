<?php

namespace App\Modules\Recipient\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Recipient\Http\Requests\Concerns\HasRecipientValidation;
use App\Modules\Recipient\Constants\RecipientConstants;

class CreateRecipientRequest extends FormRequest
{
    use HasRecipientValidation;
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('recipients.create');
    }

    public function rules(): array
    {
        $isIndividual   = $this->input('file_type') === RecipientConstants::FILE_TYPE_INDIVIDUAL;
        $isBusiness     = $this->input('file_type') === RecipientConstants::FILE_TYPE_BUSINESS;
        $isForeign      = $this->boolean('is_foreign_address');
        $w8orW9         = $this->boolean('w8_request', false) || $this->boolean('w9_request', false);

        return [
            'payer_uuid' => ['required', 'uuid', Rule::exists('payers', 'uuid')->where('user_id', auth()->id())],

            // ── Recipient type ────────────────────────────────────────────
            'file_type' => ['required', Rule::in(RecipientConstants::FILE_TYPES)],

            // ── W-8 / W-9 request flags ───────────────────────────────────
            'w8_request' => ['sometimes', 'boolean'],
            'w9_request' => ['sometimes', 'boolean'],

            // ── Name fields ───────────────────────────────────────────────
            // Individual: first_name optional, last_name required
            // Business  : business_name (mapped to last_name) required
            'first_name'    => [$isIndividual ? 'sometimes' : 'nullable', 'string', 'max:100'],
            'middle_name'   => ['nullable', 'string', 'max:100'],

            // "last_name" doubles as business_name for business recipients
            'last_name' => [
                ($isIndividual || $isBusiness) ? 'required' : 'nullable',
                'string',
                'max:255',
            ],

            'suffix'        => ['nullable', 'string', 'max:20', Rule::in(RecipientConstants::SUFFIXES)],
            'attention_to'  => ['nullable', 'string', 'max:255'],

            // ── TIN ───────────────────────────────────────────────────────
            // Required for both types unless "TIN not provided" is checked
            'recipient_tin' => [
                $this->boolean('tin_not_provided') ? 'nullable' : 'required',
                'nullable',
                'string',
                'max:20',
                'regex:/^\d{3}-\d{2}-\d{4}$/'
            ],
            'tin_not_provided' => ['boolean'],

            // ── Contact ───────────────────────────────────────────────────
            // Email is required when W-8 or W-9 is selected
            'email' => [
                $w8orW9 ? 'required' : 'nullable',
                'nullable',
                'email',
                'max:255',
            ],
            'phone_number'  => ['nullable', 'string', 'max:30'],

            // ── Address ───────────────────────────────────────────────────
            'validate_address'  => ['boolean'],
            'is_foreign_address'   => ['boolean'],

            'address_one' => [
                ($isIndividual || $isBusiness) ? 'required' : 'nullable',
                'nullable',
                'string',
                'max:255',
            ],
            'address_two' => ['nullable', 'string', 'max:255'],

            // City: required for individual/business; also required when W-8/W-9
            // selected; also required when foreign address is checked
            'city' => [
                ($isIndividual || $isBusiness || $w8orW9 || $isForeign) ? 'required' : 'nullable',
                'nullable',
                'string',
                'max:100',
            ],

            // State/Province: required for domestic individual/business;
            // optional when foreign address is checked
            'state' => [
                (($isIndividual || $isBusiness) && !$isForeign) ? 'required' : 'nullable',
                'nullable',
                'string',
                'max:100',
            ],

            // Zip/Postal Code: required for domestic individual/business;
            // optional when foreign address is checked
            'zip_code' => [
                (($isIndividual || $isBusiness) && !$isForeign) ? 'required' : 'nullable',
                'nullable',
                'string',
                'max:20',
            ],

            // Country: always required for individual/business;
            // when foreign address → must NOT be "US"/"USA"
            'country' => [
                'required_unless:w8_request,true:w9_request,true:is_foreign_address,true',
                'string',
                'max:100',
                // $isForeign
                //     ? Rule::notIn(['US', 'USA', 'United States', 'United States of America'])
                //     : 'sometimes',
            ],

            // ── Extra fields ──────────────────────────────────────────────
            'client_recipient_id' => ['nullable', 'string', 'max:255'],
            'email_language'      => ['nullable', 'string', Rule::in([
                'en',
                'es',
                'fr',
                'de',
                'zh',
                'ja',
                'ko',
                'pt',
                'ar',
                // extend with any other supported locale codes
            ])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tin_not_provided'          => $this->boolean('tin_not_provided'),
            'validate_address'          => $this->boolean('validate_address'),
            'is_foreign_address'           => $this->boolean('is_foreign_address'),
            'w8_request'                => $this->boolean('w8_request'),
            'w9_request'                => $this->boolean('w9_request'),
        ]);
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'file_type.required'                 => 'Please select a recipient type (Individual or Business).',
            'file_type.in'                        => 'Recipient type must be either individual or business.',

            'last_name.required'             => $this->input('file_type') === RecipientConstants::FILE_TYPE_BUSINESS
                ? 'Business name is required.'
                : 'Last name is required.',
            'suffix.in'                     => 'Suffix must be one of: ' . implode(', ', RecipientConstants::SUFFIXES),
            'recipient_tin.required'         => 'Recipient TIN is required unless "TIN not provided" is checked.',
            'recipient_tin.regex'            => 'Recipient TIN must be in the format 111-11-1111.',

            'email.required'         => 'Email address is required when a W-8 or W-9 request is selected.',
            'email.email'            => 'Please enter a valid email address.',

            'address_one.required'        => 'Address Line 1 is required.',
            'city.required'                  => 'City is required.',
            'state.required'                 => 'State / Province is required for domestic addresses.',
            'zip_code.required'              => 'Zip / Postal Code is required for domestic addresses.',
            'country.required'               => 'Country is required.',
            'country.not_in'                 => 'Country must not be USA when "Foreign Address" is checked.',
            'country.required_unless'        => 'Country is required unless "W-8 request" or "W-9 request" is checked.',
        ];
    }

    /**
     * Human-readable attribute names used in default Laravel messages.
     */
    public function attributes(): array
    {
        return [
            'file_type'                  => 'recipient type',
            'first_name'            => 'first name',
            'middle_name'           => 'middle name',
            'last_name'             => 'last name / business name',
            'suffix'                => 'suffix',
            'attention_to'          => 'attention to',
            'recipient_tin'         => 'recipient TIN',
            'tin_not_provided'      => 'TIN not provided',
            'email'         => 'email address',
            'phone_number'          => 'phone number',
            'validate_address'      => 'validate address',
            'is_foreign_address'       => 'is foreign address',
            'address_one'        => 'address line 1',
            'address_two'        => 'address line 2',
            'city'                  => 'city',
            'state'                 => 'state / province',
            'zip_code'              => 'zip / postal code',
            'country'               => 'country',
            'client_recipient_id'   => 'client recipient ID',
            'email_language'        => 'email language',
            'w8_request'            => 'W-8 request',
            'w9_request'            => 'W-9 request',
        ];
    }
}
