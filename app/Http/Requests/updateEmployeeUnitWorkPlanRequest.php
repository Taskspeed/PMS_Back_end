<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateEmployeeUnitWorkPlanRequest extends FormRequest
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
            'performance_standards' => 'required|array|min:1',
            'performance_standards.*.category' => 'required|string',
            'performance_standards.*.mfo' => 'nullable|string',
            'performance_standards.*.output' => 'nullable|string',
            'performance_standards.*.output_name' => 'required|string', // ✅ Should match store
            'performance_standards.*.core_competency' => 'nullable|array',
            'performance_standards.*.technical_competency' => 'nullable|array',
            'performance_standards.*.leadership_competency' => 'nullable|array',
            'performance_standards.*.success_indicator' => 'required|string',
            'performance_standards.*.performance_indicator' => 'required|array',
            'performance_standards.*.required_output' => 'nullable|string',

            'performance_standards.*.ratings' => 'required|array|min:1',
            'performance_standards.*.ratings.*.rating' => 'nullable|integer',
            'performance_standards.*.ratings.*.quantity' => 'nullable|string',
            'performance_standards.*.ratings.*.effectiveness' => 'nullable|string',
            'performance_standards.*.ratings.*.timeliness' => 'nullable|string',

            'performance_standards.*.config' => 'required|array',
            'performance_standards.*.config.target_output' => 'required|string',
            'performance_standards.*.config.quantity_indicator' => 'required|string',
            'performance_standards.*.config.timeliness_indicator' => 'required|string',

            'performance_standards.*.config.timelinessType' => 'required|array',
            'performance_standards.*.config.timelinessType.range' => 'required|boolean',
            'performance_standards.*.config.timelinessType.date' => 'required|boolean',
            'performance_standards.*.config.timelinessType.description' => 'required|boolean',
        ];
    }
}
