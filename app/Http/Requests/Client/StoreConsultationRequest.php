<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
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
            'lawyer_id' => 'nullable|exists:lawyers,id',
            'specialization_id' => 'nullable|exists:specializations,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'priority' => 'required|in:normal,urgent',
            'preferred_channel' => 'required|in:chat,meeting_link',
            'meeting_link' => 'nullable|url|required_if:preferred_channel,meeting_link',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB max
        ];
    }

    public function messages(): array
    {
        return [
            'lawyer_id.exists' => 'The selected lawyer does not exist.',
            'specialization_id.exists' => 'The selected specialization does not exist.',
            'subject.required' => 'Consultation subject is required.',
            'description.required' => 'Problem description is required.',
            'description.min' => 'The problem description must be at least 10 characters.',
            'priority.required' => 'Priority level is required.',
            'preferred_channel.required' => 'Preferred consultation method is required.',
            'preferred_channel.in' => 'Consultation method must be either chat or meeting_link.',
            'meeting_link.required_if' => 'Meeting link is required when choosing meeting_link.',
            'meeting_link.url' => 'Meeting link must be a valid URL.',
            'attachments.*.file' => 'Attachment must be a valid file.',
            'attachments.*.max' => 'File size must be less than 10 MB.',
        ];
    }
}

