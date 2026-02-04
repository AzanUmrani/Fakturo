<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateInvoiceRequest extends FormRequest
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
            'number' => ['required', 'string'],
            'billed_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'send_date' => ['required', 'date'],

            'variable_symbol' => [],
            'constant_symbol' => [],
            'specific_symbol' => [],

            'order_id' => [],

            'billed_client_id' => ['required', 'integer'],
            'items' => ['required', 'array'],

            'payment' => ['required', 'string', 'in:BANK,CASH'],
            'cash_payment_rounding' => [],
            'bank_transfer' => [],

            'note' => [],

            'totalPrice' => ['required', 'numeric'],
            'currency_3_code' => ['required', 'string', 'min:3', 'max:3'],
            'language_2_code' => ['required', 'string', 'min:2', 'max:2'],
        ];
    }
}