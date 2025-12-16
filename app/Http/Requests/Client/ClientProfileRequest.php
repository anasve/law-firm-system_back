<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientProfileRequest extends FormRequest
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
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:clients,email,' . auth('client')->id(),
            'password' => 'sometimes|string|min:8|confirmed',
            'phone'    => 'sometimes|nullable|string|max:20',
            'address'  => 'sometimes|nullable|string|max:500',
            'photo'    => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }



     public function messages(): array
    {
        return [
            'name.string'       => 'The name must be a valid string.',
            'name.max'          => 'The name must not exceed 255 characters.',

            'email.email'       => 'Please enter a valid email address.',
            'email.unique'      => 'This email is already taken.',

            'password.string'       => 'The password must be a valid string.',
            'password.min'          => 'The password must be at least 8 characters.',
            'password.confirmed'    => 'The password confirmation does not match.',
        ];
    }
}
