<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'note' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }
    
    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Please select supplier.',
            'order_date.required' => 'Please select order date.',
            'expected_date.after_or_equal' => 'Expected date must be after or equal to order date.',
            'items.required' => 'Please add at least one product item.',
            'items.min' => 'Please add at least one product item.',
            'items.*.product_id.required' => 'Please select product.',
            'items.*.quantity.required' => 'Please enter quantity.',
            'items.*.quantity.min' => 'Quantity must be greater than 0.',
            'items.*.unit_price.required' => 'Please enter unit price.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
        ];
    }
}