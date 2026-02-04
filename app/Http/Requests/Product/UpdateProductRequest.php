<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "type" => ["required", "string"],
            "name" => ["required", "string"],
            "description" => ["string"],
            "price" => ["required", "numeric"],
            "taxRate" => [],
            "discount" => ["numeric"],
            "unit" => [],
            "sku" => [],
            "weight" => ["numeric"],

            'imageBase64' => [],
            'imageTask' => 'required|string|in:delete,upload,none'
        ];
    }
}
