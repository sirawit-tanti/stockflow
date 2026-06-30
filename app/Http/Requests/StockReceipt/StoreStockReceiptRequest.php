<?php

namespace App\Http\Requests\StockReceipt;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'received_at' => 'required|date',
            'note' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.purchase_order_item_id' => 'required|integer|exists:purchase_order_item_id',
            'items.*.quantity' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'warehouse_id.required' => 'Please select warehouse.',
            'received_at.required' => 'Please select received date and time.',
            'items.required' => 'Please provide received items.',
            'items.*.quantity.required' => 'Please enter received quantity.',
            'items.*.quantity.min' => 'Received quantity cannot be negative.',
        ];
    }
}