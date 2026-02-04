<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCompanyBasicInfoRequest extends FormRequest
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
            'default' => ['boolean'],
            'name' => ['required', 'string'],

            'state' => ['required', 'string', 'max:2'],
            'street' => ['required', 'string'],
            'street_extra' => [],
            'zip' => ['required', 'string'],
            'city' => ['required', 'string'],

            'tax_type' => [],
            // ico
            'identification_number' => ['required', 'string'],
            // dic
            'vat_identification_number' => ['required', 'string'],
            // icdph (SK, HU only)
            'vat_identification_number_sk' => [],

            'registry_info' => [],

            'contact_name' => ['required', 'string'],
            'contact_phone' => ['required', 'string'],
            'contact_email' => ['required', 'string'],
            'contact_web' => [],
        ];
    }
}
