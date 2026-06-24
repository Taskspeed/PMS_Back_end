<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class syncUnitWorkPlanIpcrOpcrRequest extends FormRequest
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
            //

            'unitworkplan_id'   => 'required|array',
            'unitworkplan_id.*' => 'required|exists:unitworkplans,id',

            'employee_target_period_id' => 'required|array',
            'employee_target_period_id.*' => 'exists:target_periods,id',

            'office_opcr_id' => 'nullable|array',
            'office_opcr_id.*' => 'exists:office_opcrs,id',

            'status' => 'required|string',
            'remarks' => 'nullable|string',
        ];
    }
}
