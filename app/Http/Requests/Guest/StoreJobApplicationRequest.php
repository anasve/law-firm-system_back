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
            'email' => 'nullable|email|unique:job_applications,email',
            'phone' => 'nullable|string|max:20',
            'age' => 'required|integer|min:18|max:100',
            'address' => 'nullable|string|max:500',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max - personal photo required
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
            'type.required' => 'Application type is required (lawyer or employee).',
            'type.in' => 'Application type must be lawyer or employee.',
            'name.required' => 'Name is required.',
            'email.email' => 'Invalid email address.',
            'email.unique' => 'This email is already in use.',
            'age.required' => 'Age is required.',
            'age.min' => 'Age must be at least 18 years.',
            'age.max' => 'Invalid age.',
            'specialization_id.required' => 'Specialization is required for lawyer applications.',
            'specialization_id.exists' => 'The selected specialization does not exist.',
            'certificate.required' => 'Certificate is required for lawyer applications.',
            'certificate.file' => 'Certificate must be a file.',
            'certificate.mimes' => 'Certificate must be in PDF, DOC, or DOCX format.',
            'certificate.max' => 'Certificate size must be less than 10 MB.',
            'photo.required' => 'Personal photo is required.',
            'photo.image' => 'Personal photo must be an image (JPEG, PNG, GIF).',
            'photo.max' => 'Photo size must be less than 10 MB.',
        ];
    }
}

