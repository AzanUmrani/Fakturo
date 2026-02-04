<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',

            'state' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'street_extra' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',

            'tax_type' => 'nullable|string|max:50',
            'identification_number' => 'nullable|string|max:50',
            'vat_identification_number' => 'nullable|string|max:50',
            'vat_identification_number_sk' => 'nullable|string|max:50',

            'registry_info' => 'nullable|string',

            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_web' => 'nullable|url|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The company name is required.',
            'name.max' => 'The company name cannot exceed 255 characters.',
            'state.max' => 'The state/country cannot exceed 255 characters.',
            'street.max' => 'The street address cannot exceed 255 characters.',
            'street_extra.max' => 'The additional address information cannot exceed 255 characters.',
            'zip.max' => 'The ZIP/postal code cannot exceed 20 characters.',
            'city.max' => 'The city cannot exceed 255 characters.',
            'tax_type.max' => 'The tax type cannot exceed 50 characters.',
            'identification_number.max' => 'The identification number cannot exceed 50 characters.',
            'vat_identification_number.max' => 'The VAT identification number cannot exceed 50 characters.',
            'vat_identification_number_sk.max' => 'The VAT identification number SK cannot exceed 50 characters.',
            'contact_name.max' => 'The contact name cannot exceed 255 characters.',
            'contact_phone.max' => 'The contact phone cannot exceed 50 characters.',
            'contact_email.email' => 'Please enter a valid email address.',
            'contact_email.max' => 'The contact email cannot exceed 255 characters.',
            'contact_web.url' => 'Please enter a valid URL (including http:// or https://).',
            'contact_web.max' => 'The website URL cannot exceed 255 characters.',
        ];
    }
}
