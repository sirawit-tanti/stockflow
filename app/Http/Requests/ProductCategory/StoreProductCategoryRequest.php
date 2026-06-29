<?php

namespace App\Http\Requests\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:product_categories,name'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean']
        ];
    }

    public function messages() : array 
    {
        return [
            'name.required' => 'Please enter category name.',
            'name.unique' => 'This category name already existx.',
            'is_active.required' => 'Please select category status.',
        ];    
    }
}