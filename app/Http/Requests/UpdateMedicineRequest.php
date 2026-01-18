<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'dosage' => ['required', 'string', 'max:255'],
            'schedule_type' => ['required', Rule::in(['hours', 'days', 'weekdays', 'times', 'dates', 'as_needed'])],
            'frequency_hours' => [
                Rule::requiredIf($this->input('schedule_type') === 'hours'),
                'nullable',
                'integer',
                'min:0',
                'max:168',
            ],
            'frequency_days' => [
                Rule::requiredIf($this->input('schedule_type') === 'days'),
                'nullable',
                'integer',
                'min:0',
                'max:365',
            ],
            'weekdays' => [
                Rule::requiredIf($this->input('schedule_type') === 'weekdays'),
                'nullable',
                'array',
                'min:1',
            ],
            'weekdays.*' => ['in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
            'time_of_day' => [
                Rule::requiredIf(in_array($this->input('schedule_type'), ['days', 'weekdays', 'dates'], true)),
                'nullable',
                'date_format:H:i',
            ],
            'times_input' => [
                Rule::requiredIf($this->input('schedule_type') === 'times'),
                'nullable',
                'string',
            ],
            'dates_input' => [
                Rule::requiredIf($this->input('schedule_type') === 'dates'),
                'nullable',
                'string',
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
            'next_dose_at' => ['nullable', 'date'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
