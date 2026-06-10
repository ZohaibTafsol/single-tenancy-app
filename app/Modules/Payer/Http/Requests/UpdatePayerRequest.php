<?php

namespace App\Modules\Payer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Payer\Http\Requests\Concerns\HasPayerValidation;
use App\Modules\Payer\Constants\PayerConstants;

class UpdatePayerRequest extends FormRequest
{
    use HasPayerValidation;
    public function authorize(): bool
    {
    return auth()->user()->hasPermissionTo('payers.update');
    }

    public function rules(): array
    {
        $isIndividual = $this->input('file_type') === PayerConstants::FILE_TYPE_INDIVIDUAL;
        $isBusiness   = $this->input('file_type') === PayerConstants::FILE_TYPE_BUSINESS;
        $isForeign    = $this->boolean('is_foreign_address');

        // Resolve the payer being updated from the route parameter.
        // The route is: PUT /payers/{uuid}  — adjust 'uuid' if your route param differs.
        $payerUuid = $this->route('uuid');

        return [
            'file_type' => ['required', Rule::in([PayerConstants::FILE_TYPE_INDIVIDUAL, PayerConstants::FILE_TYPE_BUSINESS])],

            'name' => [$isBusiness ? 'required' : 'nullable', 'string', 'max:100'],

            'first_name'  => $isIndividual ? ['nullable', 'string', 'max:100']  : ['prohibited'],
            'middle_name' => $isIndividual ? ['nullable', 'string', 'max:100']  : ['prohibited'],
            'last_name'   => $isIndividual ? ['required', 'string', 'max:100']  : ['prohibited'],
            'suffix'      => $isIndividual
                ? ['nullable', 'string', Rule::in(PayerConstants::SUFFIXES)]
                : ['prohibited'],

            'id_type'   => ['required', Rule::in([PayerConstants::ID_TYPE_SSN, PayerConstants::ID_TYPE_EIN])],
            'id_number' => [
                'required',
                'string',
                'max:11',
                $isIndividual
                    ? 'regex:/^\d{3}-\d{2}-\d{4}$/'
                    : 'regex:/^\d{2}-\d{7}$/',
            ],

            'email'              => ['nullable', 'email:rfc', 'max:255'],
            'phone_number'       => ['required', 'string', 'max:20', 'regex:/^\+?[\d\s\-().]{7,20}$/'],
            'disregarded_entity' => ['nullable', 'string', 'max:200'],

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
            'country'            => ['required', 'string', 'size:2'],
            'is_foreign_address' => ['boolean'],

            'withholding_tax_state_id' => ['nullable', 'string', 'max:100'],
            'client_payer_id'          => ['nullable', 'string', 'max:100'],
            'group_id'                 => ['nullable', 'string', 'max:100'],
            'is_last_filing'           => ['boolean'],

            // KEY DIFFERENCE from create: ignore THIS payer's own uuid in the unique check
            'payer_detail_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('payers', 'payer_detail_id')->whereNot('uuid', $payerUuid),
            ],

            'tin_status'            => ['nullable', 'string', 'max:50'],
            'is_tin_check'          => ['boolean'],
            'un_mask_recipient_tin' => ['boolean'],
            'trade_name'            => ['nullable', 'string', 'max:200'],
        ];
    }

    // messages(), attributes(), prepareForValidation(), usStates()
    // are identical to CreatePayerRequest — extract to a shared trait (see below)
}
