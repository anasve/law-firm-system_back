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
            'lawyer_id.exists' => 'المحامي المحدد غير موجود.',
            'specialization_id.exists' => 'التخصص المحدد غير موجود.',
            'subject.required' => 'عنوان الاستشارة مطلوب.',
            'description.required' => 'وصف المشكلة مطلوب.',
            'description.min' => 'وصف المشكلة يجب أن يكون على الأقل 10 أحرف.',
            'priority.required' => 'درجة الأولوية مطلوبة.',
            'preferred_channel.required' => 'طريقة الاستشارة المفضلة مطلوبة.',
            'preferred_channel.in' => 'طريقة الاستشارة يجب أن تكون إما شات أو رابط اجتماع.',
            'meeting_link.required_if' => 'رابط الاجتماع مطلوب عند اختيار طريقة رابط الاجتماع.',
            'meeting_link.url' => 'رابط الاجتماع يجب أن يكون رابطاً صحيحاً.',
            'attachments.*.file' => 'يجب أن يكون المرفق ملفاً صحيحاً.',
            'attachments.*.max' => 'حجم الملف يجب أن يكون أقل من 10 ميجابايت.',
        ];
    }
}

