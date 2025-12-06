<?php

namespace App\Http\Requests\Admin\law;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLawRequest extends FormRequest
{
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
            'title'        => 'sometimes|required|string|max:255',
            'category'     => 'sometimes|required|string|max:255',
            'summary'      => 'sometimes|required|string|max:500',
            'full_content' => 'sometimes|required|string',
            'status'       => 'sometimes|in:draft,published',
        ];
    }
}
