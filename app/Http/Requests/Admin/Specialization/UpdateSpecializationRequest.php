<?php

namespace App\Http\Requests\Admin\Specialization;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecializationRequest extends FormRequest
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
        $id = $this->route('specialization'); // get specialization ID from URL

        return [
            'name'        => 'nullable|string|max:100|unique:specializations,name,' . $id,
            'description' => 'nullable|string|max:500',
        ];
    }
}
