<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Recipient\Http\Requests\Concerns\HasRecipientValidation;

class CreateRecipientRequest extends FormRequest
{
    use HasRecipientValidation;
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('recipient.create');
    }

    public function rules(): array
    {
        $isIndividual = $this->input('file_type') === 'Individual';
        $isForeign    = $this->boolean('is_foreign_address');
        $tinProvided  = ! $this->boolean('tin_not_provided');

        return [

            // ── Type ──────────────────────────────────────────────────
            'file_type' => ['required', Rule::in(['Individual', 'Business'])],

            // ── W-8 / W-9 ─────────────────────────────────────────────
            'w8_request' => ['sometimes', 'boolean'],
            'w9_request' => ['sometimes', 'boolean'],

            // ── Basic Information ──────────────────────────────────────
            // Individual fields
            'first_name'  => [Rule::requiredIf($isIndividual), 'nullable', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name'   => [Rule::requiredIf($isIndividual), 'nullable', 'string', 'max:100'],
            'suffix'      => ['nullable', Rule::in(['Jr', 'Sr', '2nd', '3rd', 'II', 'III', 'IV', 'V', 'VI'])],

            // Business field
            'name' => [Rule::requiredIf(! $isIndividual), 'nullable', 'string', 'max:200'],

            // ── TIN ───────────────────────────────────────────────────
            'tin_not_provided' => ['sometimes', 'boolean'],
            'tin_type'         => [
                Rule::requiredIf($tinProvided),
                'nullable',
                Rule::in(['SSN', 'EIN', 'ITIN', 'ATIN']),
            ],
            'tin' => [
                Rule::requiredIf($tinProvided),
                'nullable',
                'string',
                'max:11',
                // SSN: 123-45-6789  |  EIN: 12-3456789
                function (string $attribute, mixed $value, \Closure $fail) use ($isIndividual) {
                    if (! $value) {
                        return;
                    }
                    $ssnPattern = '/^\d{3}-\d{2}-\d{4}$/';
                    $einPattern = '/^\d{2}-\d{7}$/';

                    if ($isIndividual && ! preg_match($ssnPattern, $value)) {
                        $fail('The TIN must be in SSN format: ###-##-####.');
                    }

                    if (! $isIndividual && ! preg_match($einPattern, $value)) {
                        $fail('The TIN must be in EIN format: ##-#######.');
                    }
                },
            ],

            // ── Contact ───────────────────────────────────────────────
            'attention_to'  => ['nullable', 'string', 'max:200'],
            'email'         => ['nullable', 'email:rfc,dns', 'max:255'],
            'phone_number'  => ['nullable', 'string', 'max:20'],

            // ── Address ────────────────────────────────────────────────
            'address_one'       => ['required', 'string', 'max:255'],
            'address_two'       => ['nullable', 'string', 'max:255'],
            'city'              => ['required', 'string', 'max:100'],
            'state'             => [
                Rule::requiredIf(! $isForeign),
                'nullable',
                'string',
                'size:2',
                'alpha',
            ],
            'zip_code' => [
                Rule::requiredIf(! $isForeign),
                'nullable',
                'string',
                'max:10',
                'regex:/^\d{5}(-\d{4})?$/',
            ],
            'country'            => ['required', 'string', 'size:2', 'alpha'],
            'is_foreign_address' => ['sometimes', 'boolean'],

            // ── Optional / Client Fields ───────────────────────────────
            'client_recipient_id' => ['nullable', 'string', 'max:100'],
            'email_language'      => ['nullable', 'string', 'max:10'],

            // ── 1099 Form Flags ────────────────────────────────────────
            'account_number'            => ['nullable', 'string', 'max:100'],
            'second_tin_notice'         => ['nullable', 'string', 'max:10'],
            'fatca_filing_requirement'  => ['sometimes', 'boolean'],
            'is_last_filing'            => ['sometimes', 'boolean'],

            // ── tax1099 Sync ───────────────────────────────────────────
            'is_tin_check'          => ['sometimes', 'boolean'],
            'un_mask_recipient_tin' => ['sometimes', 'boolean'],
        ];
    }
}