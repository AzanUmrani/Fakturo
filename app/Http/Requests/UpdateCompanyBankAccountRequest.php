<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCompanyBankAccountRequest extends FormRequest
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
            'bank_transfer.name' => ['required', 'string'],
            'bank_transfer.code' => ['required', 'string'],
            'bank_transfer.iban' => ['required', 'string'],
            'bank_transfer.swift' => ['required', 'string'],
        ];
    }
}
