<?php

namespace App\Http\Requests\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories', 'name')
                    ->ignore($this->route('product_category')),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter category name.',
            'name.unique' => 'This category name already exists.',
            'is_active.required' => 'Please select category status.',
        ];
    }
}