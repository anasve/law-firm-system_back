<?php
namespace App\Http\Requests\Admin\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
            'name'     => 'sometimes|string|max:100',
            'email'    => 'sometimes|email|unique:employees,email,' . $this->route('employee'),
            'password' => 'sometimes|nullable|string|min:6|confirmed',
            'age'      => 'sometimes|required|integer|min:18',
            'phone'    => 'sometimes|nullable|string|max:20',
            'address'  => 'sometimes|nullable|string|max:500',
            'photo'    => 'sometimes|nullable|image',

        ];
    }

    public function messages()
    {
        return [
            'email.email'  => 'Please provide a valid email address.',
            'email.unique' => 'This email is already in use.',
            'age.min'      => 'Lawyer must be at least 18 years old.',

        ];
    }
}
