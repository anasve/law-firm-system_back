<?php
namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientLoginRequest extends FormRequest
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
            'email'    => 'required|email|exists:clients,email',
            'password' => 'required|string|min:6',
        ];

    }

    public function messages(): array
    {
        return [
            'email.required'    => 'The email address is required.',
            'email.email'       => 'Please enter a valid email address.',
            'email.exists'      => 'This email is not registered.',

            'password.required' => 'The password is required.',
            'password.string'   => 'The password must be a string.',
            'password.min'      => 'The password must be at least 6 characters.',
        ];
    }
}
