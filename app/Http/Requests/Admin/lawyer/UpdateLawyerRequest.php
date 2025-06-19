<?php
namespace App\Http\Requests\Admin\Lawyer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLawyerRequest extends FormRequest
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
            'name'                 => 'sometimes|required|string|max:255',

            'email'                => [
                'sometimes',
                'required',
                'email',
                Rule::unique('lawyers')->ignore($this->route('laywer')),
            ],

            'password'             => 'sometimes|nullable|string|min:6|confirmed',

            'age'                  => 'sometimes|required|integer|min:18',

            'photo'                => 'sometimes|nullable|image',
            'certificate'          => 'sometimes|nullable|file',

            'specialization_ids.*' => 'exists:specializations,id',
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
