<?php

namespace App\Http\Requests\Guest;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Anyone can submit a job application
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            // Common fields
            'type' => 'required|in:lawyer,employee',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:job_applications,email',
            'phone' => 'nullable|string|max:20',
            'age' => 'required|integer|min:18|max:100',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
        ];

        // Lawyer-specific rules
        if ($this->input('type') === 'lawyer') {
            $rules['specialization_id'] = 'required|exists:specializations,id';
            $rules['experience_years'] = 'nullable|integer|min:0|max:50';
            $rules['bio'] = 'nullable|string|max:1000';
            $rules['certificate'] = 'required|file|mimes:pdf,doc,docx|max:10240'; // 10MB max
        }

        // Employee-specific rules
        if ($this->input('type') === 'employee') {
            $rules['experience_years'] = 'nullable|integer|min:0|max:50';
            $rules['bio'] = 'nullable|string|max:1000';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'نوع الطلب مطلوب (lawyer أو employee)',
            'type.in' => 'نوع الطلب يجب أن يكون lawyer أو employee',
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            'age.required' => 'العمر مطلوب',
            'age.min' => 'يجب أن يكون العمر 18 سنة على الأقل',
            'age.max' => 'العمر غير صحيح',
            'specialization_id.required' => 'التخصص مطلوب للمحامي',
            'specialization_id.exists' => 'التخصص المحدد غير موجود',
            'certificate.required' => 'الشهادة مطلوبة للمحامي',
            'certificate.file' => 'الشهادة يجب أن تكون ملف',
            'certificate.mimes' => 'الشهادة يجب أن تكون بصيغة PDF, DOC, أو DOCX',
            'certificate.max' => 'حجم الشهادة يجب أن يكون أقل من 10 ميجابايت',
            'photo.image' => 'الصورة يجب أن تكون صورة',
            'photo.max' => 'حجم الصورة يجب أن يكون أقل من 10 ميجابايت',
        ];
    }
}

