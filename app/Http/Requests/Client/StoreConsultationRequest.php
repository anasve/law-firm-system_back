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
            'preferred_channel' => 'required|in:chat,in_office,call,appointment',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB max
            // إذا اختار appointment، يجب إرسال بيانات الموعد
            'appointment_availability_id' => 'nullable|required_if:preferred_channel,appointment|exists:lawyer_availability,id',
            'appointment_type' => 'nullable|required_if:preferred_channel,appointment|in:online,in_office,phone',
            'appointment_meeting_link' => 'nullable|url|required_if:appointment_type,online',
            'appointment_notes' => 'nullable|string|max:1000',
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
            'appointment_availability_id.required_if' => 'يجب اختيار موعد عند اختيار طريقة الموعد.',
            'appointment_type.required_if' => 'نوع الموعد مطلوب.',
            'appointment_meeting_link.required_if' => 'رابط الاجتماع مطلوب للاجتماعات الافتراضية.',
            'attachments.*.file' => 'يجب أن يكون المرفق ملفاً صحيحاً.',
            'attachments.*.max' => 'حجم الملف يجب أن يكون أقل من 10 ميجابايت.',
        ];
    }
}

