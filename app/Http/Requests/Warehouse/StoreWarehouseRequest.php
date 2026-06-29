<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:100|unique:warehouses,code',
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active' => 'required|boolean'
        ];
    }
    
    public function messages(): array
    {
        return [
            'code.required' => 'Please enter warehouse code.',
            'code.unique' => 'This warehouse code already exists.',
            'name.required' => 'Please enter warehouse name.',
            'is_active.required' => 'Please select warehouse status.',
        ];
    }
}