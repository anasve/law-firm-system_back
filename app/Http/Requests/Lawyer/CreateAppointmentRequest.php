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
            'type' => 'required|in:online,in_office,phone',
            'meeting_link' => 'nullable|url|required_if:type,online',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}

