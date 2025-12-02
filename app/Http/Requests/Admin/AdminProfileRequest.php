<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . auth()->id(),
            'password' => 'nullable|string|min:6|confirmed', // password + password_confirmation
        ];
    }
    
    public function messages(): array
    {
        return [
            'email.unique'       => 'This email is already taken by another lawyer.',
            'age.min'            => 'Lawyer must be at least 18 years old.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
