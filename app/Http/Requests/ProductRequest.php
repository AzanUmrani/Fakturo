<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * The allowed product types.
     *
     * @var array
     */
    public const PRODUCT_TYPES = [
        'product',
        'service',
    ];

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
            'type' => 'required|string|in:' . implode(',', self::PRODUCT_TYPES),
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'taxRate' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'sku' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'has_image' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'name.required' => 'The product name is required.',
            'type.required' => 'The product type is required.',
            'type.in' => 'The selected product type is invalid. Please select a valid type.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price cannot be negative.',
            'taxRate.required' => 'The tax rate is required.',
            'taxRate.numeric' => 'The tax rate must be a number.',
            'taxRate.min' => 'The tax rate cannot be negative.',
        ];
    }
}
