<?php
namespace App\Http\Requests\Admin\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:employees,email',
            'password' => 'required|string|min:6|confirmed',
            'age'      => 'required|integer|min:18',
            'photo'    => 'nullable|image',
        ];
    }

    public function messages()
    {
        return [
            'name.required'  => 'The name is required.',
            'email.required' => 'The email is required.',
            'email.email'    => 'Please provide a valid email address.',
            'email.unique'   => 'This email is already in use.',
        ];
    }
}
