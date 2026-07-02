<?php

namespace App\Http\Requests\UserManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|string|exists:roles,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter user name.',
            'email.required' => 'Please enter email.',
            'email.email' => 'Please enter a valid email.',
            'email.unique' => 'This email is already used.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'roles.required' => 'Please select at least one role.',
            'roles.min' => 'Please select at least one role.',
            'roles.*.exists' => 'Selected role is invalid.',
        ];
    }
}