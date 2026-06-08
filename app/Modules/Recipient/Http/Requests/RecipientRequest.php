<?php

namespace App\Modules\Recipient\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RecipientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer', 'exists:Recipients,id'],
            'payer_id' => ['required','integer'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'last_name' => ['required_without:business_name', 'string', 'max:100'],
            'business_name' => ['required_without:last_name', 'string', 'max:255'],
            'attention_to' => ['nullable', 'string', 'max:100'],
            'ssn' => ['required_without:ein', 'string', 'regex:/^\d{3}-\d{2}-\d{4}$/'],
            'ein' => ['required_without:ssn', 'string', 'regex:/^\d{2}-\d{7}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'disregarded_entity' => ['nullable', 'string', 'max:255'],
            'address1' => ['required', 'string', 'max:255'],
            'address2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zipcode' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.integer' => 'ID must be a valid integer.',
            'id.exists' => 'Recipient not found.',

            'last_name.required_without' => 'Last name is required when business name is not provided.',
            'last_name.max' => 'Last name must not exceed 100 characters.',

            'business_name.required_without' => 'Business name is required when last name is not provided.',
            'business_name.max' => 'Business name must not exceed 255 characters.',

            'ssn.required_without' => 'SSN is required when EIN is not provided.',
            'ssn.regex' => 'SSN must be in the format 123-45-6789.',

            'ein.required_without' => 'EIN is required when SSN is not provided.',
            'ein.regex' => 'EIN must be in the format 12-3456789.',

            'email.email' => 'Please provide a valid email address.',

            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'phone.max' => 'Phone number must not exceed 20 digits.',

            'address1.required' => 'Address line 1 is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'zipcode.required' => 'Zip code is required.',
            'country.required' => 'Country is required.',
        ];
    }
}