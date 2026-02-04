<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'street_extra' => 'nullable|string|max:255',
            'zip' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'identification_number' => 'required|string|max:255',
            'vat_identification_number' => 'required|string|max:255',
            'vat_identification_number_sk' => 'nullable|string|max:255',
            'registry_info' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_web' => 'nullable|url|max:255',
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The client name is required.',
            'state.required' => 'The state/country is required.',
            'street.required' => 'The street address is required.',
            'zip.required' => 'The ZIP/postal code is required.',
            'city.required' => 'The city is required.',
            'identification_number.required' => 'The identification number (ICO) is required.',
            'vat_identification_number.required' => 'The VAT identification number (DIC) is required.',
            'contact_email.email' => 'Please enter a valid email address.',
            'contact_web.url' => 'Please enter a valid URL (including http:// or https://).',
        ];
    }
}
