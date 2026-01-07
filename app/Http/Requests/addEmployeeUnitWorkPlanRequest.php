<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class addEmployeeUnitWorkPlanRequest extends FormRequest
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

            'employees' => 'required|array|min:1',

            // employee details
            'employees.*.control_no' => 'required|string',
            'employees.*.office' => 'required|string',
            'employees.*.office2' => 'nullable|string',
            'employees.*.group' => 'nullable|string',
            'employees.*.division' => 'nullable|string',
            'employees.*.section' => 'nullable|string',
            'employees.*.unit' => 'nullable|string',

            // semester and year
            'employees.*.semester' => 'required|string',
            'employees.*.year' => 'required|integer',

            // performance standards
            'employees.*.performance_standards' => 'required|array|min:1',
            'employees.*.performance_standards.*.category' => 'required|string',
            'employees.*.performance_standards.*.mfo' => 'required|string',
            'employees.*.performance_standards.*.output' => 'required|string',
            'employees.*.performance_standards.*.core_competency' => 'nullable|array',
            'employees.*.performance_standards.*.technical_competency' => 'nullable|array',
            'employees.*.performance_standards.*.leadership_competency' => 'nullable|array',
            'employees.*.performance_standards.*.success_indicator' => 'required|string',
            'employees.*.performance_standards.*.performance_indicator' => 'required|string',
            'employees.*.performance_standards.*.required_output' => 'required|string',

            // standatd outcomes / ratings
            'employees.*.performance_standards.*.ratings' => 'required|array|min:1',
            'employees.*.performance_standards.*.ratings.*.rating' => 'nullable|integer',
            'employees.*.performance_standards.*.ratings.*.quantity' => 'nullable|string',
            'employees.*.performance_standards.*.ratings.*.effectiveness' => 'nullable|string',
            'employees.*.performance_standards.*.ratings.*.timeliness' => 'nullable|string',

            'employees.*.performance_standards.*.config' => 'required|array',
            'employees.*.performance_standards.*.config.*.quantity' => 'required|string',
            'employees.*.performance_standards.*.config.*.timeliness' => 'required|string',
            'employees.*.performance_standards.*.config.*.type' => 'required|string',

        ];
    }
}
