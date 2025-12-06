<?php

namespace App\Http\Requests\Admin\law;

use Illuminate\Foundation\Http\FormRequest;

class StoreLawRequest extends FormRequest
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
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|max:255',
            'summary'      => 'required|string|max:500',
            'full_content' => 'required|string',
            'status'       => 'nullable|in:draft,published',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'العنوان مطلوب.',
            'category.required' => 'التصنيف مطلوب.',
        ];
    }
}
