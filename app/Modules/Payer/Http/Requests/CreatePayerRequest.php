<?php

namespace App\Modules\Payer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Payer\Http\Requests\Concerns\HasPayerValidation;
use App\Modules\Payer\Constants\PayerConstants;

class CreatePayerRequest extends FormRequest
{
    use HasPayerValidation;
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('payers.create');
    }

    public function rules(): array
    {
        $isIndividual = $this->input('file_type') === PayerConstants::FILE_TYPE_INDIVIDUAL;
        $isBusiness   = $this->input('file_type') === PayerConstants::FILE_TYPE_BUSINESS;
        $isForeign    = $this->boolean('is_foreign_address');

        return [
            // ── Type ──────────────────────────────────────────────────
            'file_type' => ['required', Rule::in([PayerConstants::FILE_TYPE_INDIVIDUAL, PayerConstants::FILE_TYPE_BUSINESS])],

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
                ? ['nullable', 'string', Rule::in(PayerConstants::SUFFIXES)]
                : ['prohibited'],

            // ── ID Number (SSN for Individual, EIN for Business) ──────
            'id_type' => ['required', Rule::in([PayerConstants::ID_TYPE_SSN, PayerConstants::ID_TYPE_EIN])],

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
}