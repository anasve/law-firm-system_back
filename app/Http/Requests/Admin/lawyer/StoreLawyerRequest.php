<?php
namespace App\Http\Requests\Admin\Lawyer;

use Illuminate\Foundation\Http\FormRequest;

class StoreLawyerRequest extends FormRequest
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
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:lawyers,email',
            'age'                => 'required|integer|min:18',
            'photo'              => 'nullable|image',
            'password'           => 'required|string|min:6|confirmed',
            'certificate'        => 'nullable|file',
            'specialization_ids' => 'required|array',
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
