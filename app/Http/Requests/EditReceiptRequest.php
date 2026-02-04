<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'billed_to_client_id' => 'required|exists:clients,id',
            'language_2_code' => 'nullable|string|size:2',
            'total' => 'required|numeric|min:0',
            'currency_3_code' => 'required|string|size:3',
            'purpose' => 'nullable|string',
            'made_by' => 'nullable|string',
            'approved_by' => 'nullable|string',
            'journal_number' => 'nullable|string',
            'billing_regulation' => 'nullable|array',
            'billing_regulation.*.account' => 'required_with:billing_regulation|string',
            'billing_regulation.*.total' => 'required_with:billing_regulation|numeric|min:0',
            'date' => 'required|date',
        ];
    }
}
