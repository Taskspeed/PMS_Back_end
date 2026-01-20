<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class performanceRatingStoreRequest extends FormRequest
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
            'performance_rate' => 'required|array|min:1',
            'performance_rate.*.performance_standards' => 'required|exists:performance_standards,id',
            'performance_rate.*.date' => 'required|date_format:m/d/Y',
            'performance_rate.*.control_no' => 'required|string',
            'performance_rate.*.quantity_target_rate' => 'required|string',
            'performance_rate.*.effectiveness_criteria_rate' => 'required|string',
            'performance_rate.*.timeliness_range_rate' => 'required|string',
        ];
    }

    public function messages()
        {
        return [
            'performance_rate.required' => 'Please add at least one performance rating.',
            'performance_rate.min' => 'Please add at least one performance rating.',

            'performance_rate.*.performance_standards.required' =>
            'Performance standard is required.',

            'performance_rate.*.date.required' =>
            'Date is required.',
            'performance_rate.*.date.date_format' =>
            'Date must be in MM/DD/YYYY format.',

            'performance_rate.*.control_no.required' =>
            'Employee control number is required.',

            'performance_rate.*.quantity_target_rate.required' =>
            'Quantity rating is required.',

            'performance_rate.*.effectiveness_criteria_rate.required' =>
            'Effectiveness rating is required.',

            'performance_rate.*.timeliness_range_rate.required' =>
            'Timeliness rating is required.',
        ];
    }
}
