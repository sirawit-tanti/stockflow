<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:50'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages() : array
    {
        return [
            'sku.required' => 'Please enter product SKU.',
            'sku.unique' => 'This SKU already exists.',
            'name.required' => 'Please enter product name.',
            'unit.required' => 'Please enter product unit.',
            'minimum_stock.required' => 'Please enter minimum stock.',
            'minimum_stock.numeric' => 'Minimum stock must be a number.',
        ];
    }
}