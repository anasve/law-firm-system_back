<?php
namespace App\Http\Requests\Lawyer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LawyerProfileRequest extends FormRequest
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
            'photo'                => 'sometimes|nullable|image|max:2048',
            'certificate'          => 'sometimes|nullable|file|max:5120',
            'specialization_ids.*' => 'exists:specializations,id',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'       => 'This email is already in use.',
            'password.confirmed' => 'Password confirmation does not match.',
            'age.min'            => 'You must be at least 18 years old.',
        ];
    }
}
