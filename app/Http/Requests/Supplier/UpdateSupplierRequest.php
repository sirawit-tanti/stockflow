<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('suppliers', 'code')->ignore($this->route('supplier')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'This supplier code already exists.',
            'name.required' => 'Please enter supplier name.',
            'email.email' => 'Please enter a valid email address.',
            'is_active.required' => 'Please select supplier status.',
        ];
    }
}