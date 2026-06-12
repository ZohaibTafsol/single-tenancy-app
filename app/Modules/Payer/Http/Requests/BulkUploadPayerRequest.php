<?php

namespace App\Modules\Payer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUploadPayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'], // 5MB max
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please upload a CSV file.',
            'file.mimes'    => 'Only CSV files are allowed.',
            'file.max'      => 'File size must not exceed 5MB.',
        ];
    }
}
