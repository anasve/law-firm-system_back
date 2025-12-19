<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'issue_date' => 'required|date',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'fixed_fee' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.type' => 'required|in:fee,copy,stamp,translation,court_fee,document,other',
            'items.*.date' => 'required|date',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.fixed_price_id' => 'nullable|exists:fixed_prices,id',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'العميل مطلوب',
            'client_id.exists' => 'العميل غير موجود',
            'issue_date.required' => 'تاريخ الإصدار مطلوب',
            'items.required' => 'يجب إضافة بنود على الأقل',
            'items.min' => 'يجب إضافة بند واحد على الأقل',
        ];
    }
}

