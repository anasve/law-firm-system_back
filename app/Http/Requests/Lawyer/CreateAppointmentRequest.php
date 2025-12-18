<?php

namespace App\Http\Requests\Lawyer;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'datetime' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}

