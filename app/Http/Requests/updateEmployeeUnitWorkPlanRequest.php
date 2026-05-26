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

            // 'target_period_id' => 'required|integer|exists:target_periods,id', // ADD THIS

            'performance_standards' => 'required|array|min:1',
            'performance_standards.*.performanceStandardId' => 'nullable|integer',
            'performance_standards.*.target_period_id' => 'required|integer', // ADD THIS too

            'performance_standards.*.category' => 'required|string',
            'performance_standards.*.mfo' => 'nullable|string',
            'performance_standards.*.output' => 'nullable|string',
            'performance_standards.*.output_name' => 'nullable|string', // Should match store
            'performance_standards.*.core_competency'                  => 'nullable|array',
            'performance_standards.*.technical_competency'             => 'nullable|array',
            'performance_standards.*.leadership_competency'            => 'nullable|array',

            'performance_standards.*.success_indicator' => 'required|string',
            'performance_standards.*.performance_indicator' => 'nullable',
            'performance_standards.*.performance_indicator.*.category' => 'nullable|string',
            'performance_standards.*.performance_indicator.*.value' => 'nullable|string',
            'performance_standards.*.supervisory_control_no' => 'nullable|string',
            'performance_standards.*.required_output' => 'nullable|string',

            // standatd outcomes / ratings
            'performance_standards.*.ratings' => 'required|array|min:1',
            'performance_standards.*.ratings.*.ratingId'        => 'nullable|integer',
            'performance_standards.*.ratings.*.quantity' => 'nullable|string',
            'performance_standards.*.ratings.*.effectiveness' => 'nullable|string',
            'performance_standards.*.ratings.*.timeliness' => 'nullable|string',
            'performance_standards.*.ratings.*.rating' => 'required|string',
            'performance_standards.*.config' => 'required|array',
            'performance_standards.*.config.configurationId'    => 'nullable|integer',
            'performance_standards.*.config.targetOutput'       => 'required|string',
            'performance_standards.*.config.quantityIndicator'  => 'required|string',
            'performance_standards.*.config.timelinessIndicator' => 'required|string',

            'performance_standards.*.config.timelinessType' => 'required|array',
            'performance_standards.*.config.timelinessType.range' => 'required|boolean',
            'performance_standards.*.config.timelinessType.date' => 'required|boolean',
            'performance_standards.*.config.timelinessType.description' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'performance_standards.required'        => 'At least one performance standard is required.',
            'performance_standards.array'           => 'Performance standards must be a list.',
            'performance_standards.min'             => 'At least one performance standard is required.',

            'performance_standards.*.category.required'        => 'Each standard must have a category.',
            'performance_standards.*.success_indicator.required' => 'Each standard must have a success indicator.',
            'performance_standards.*.ratings.required'         => 'Each standard must have at least one rating.',
            'performance_standards.*.ratings.min'              => 'Each standard must have at least one rating.',

            'performance_standards.*.config.required'                          => 'Each standard must have a config.',
            'performance_standards.*.config.target_output.required'            => 'Target output is required in config.',
            'performance_standards.*.config.quantity_indicator.required'       => 'Quantity indicator is required in config.',
            'performance_standards.*.config.timeliness_indicator.required'     => 'Timeliness indicator is required in config.',
            'performance_standards.*.config.timelinessType.required'           => 'Timeliness type is required in config.',
            'performance_standards.*.config.timelinessType.range.required'     => 'Timeliness range (true/false) is required.',
            'performance_standards.*.config.timelinessType.date.required'      => 'Timeliness date (true/false) is required.',
            'performance_standards.*.config.timelinessType.description.required' => 'Timeliness description (true/false) is required.',
        ];
    }
}
