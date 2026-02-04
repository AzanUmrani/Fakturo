<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditClientRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],

            'state' => ['required', 'string', 'max:2'],
            'street' => ['required', 'string'],
            'street_extra' => [],
            'zip' => ['required', 'string'],
            'city' => ['required', 'string'],

            // ico
            'identification_number' => [],
            // dic
            'vat_identification_number' => [],
            // icdph
            'vat_identification_number_sk' => [],

            'registry_info' => [],

            'contact_name' => [],
            'contact_phone' => [],
            'contact_email' => [],
            'contact_web' => [],
        ];
    }
}
