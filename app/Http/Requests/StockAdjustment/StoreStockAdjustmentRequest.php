<?php

namespace App\Http\Requests\StockAdjustment;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'adjustment_type' => 'required|string|in:ADJUST_IN,ADJUST_OUT',
            'adjusted_at' => 'required|date',
            'reason' => 'required|string|max:1000',
            'note' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ];
    }

    public function messages(): array
    {
        return [
            'warehouse_id.required' => 'Please select warehouse.',
            'adjustment_type.required' => 'Please select adjustment type.',
            'adjustment_type.in' => 'Adjustment type must be adjust in or adjust out.',
            'adjusted_at.required' => 'Please select adjustment date and time.',
            'reason.required' => 'Please enter adjustment reason.',
            'items.required' => 'Please add at least one adjustment item.',
            'items.min' => 'Please add at least one adjustment item.',
            'items.*.product_id.required' => 'Please select product.',
            'items.*.quantity.required' => 'Please enter quantity.',
            'items.*.quantity.min' => 'Quantity must be greater than 0.',
        ];
    }
}
