<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class addEmployeeRequest extends FormRequest
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
            'employees' => 'required|array',
            'employees.*.ControlNo' => 'required|string',
            'employees.*.name' => 'required|string|max:255',
            // 'employees.*.position_id' => 'required|exists:positions,id',
            'employees.*.office_id' => 'required|exists:offices,id',
            'employees.*.office' => 'nullable|string|max:255',
            'employees.*.position' => 'required|string|max:255', // changed from 'designation' to 'position'
            'employees.*.office2' => 'nullable|string|max:255',
            'employees.*.group' => 'nullable|string|max:255',
            'employees.*.division' => 'nullable|string|max:255',
            'employees.*.section' => 'nullable|string|max:255',
            'employees.*.unit' => 'nullable|string|max:255',
            'employees.*.rank' => 'nullable|in:Supervisor,Employee,Rank-in-File,Managerial,Section-Head,Office-Head,Division-Head',

            'employees.*.tblStructureID' => 'nullable|string|max:255',
            'employees.*.sg' => 'nullable|string|max:255',
            'employees.*.level' => 'nullable|string|max:255',
            'employees.*.positionID' => 'nullable|string|max:255',
            'employees.*.itemNo' => 'nullable|string|max:255',
            'employees.*.pageNo' => 'nullable|string|max:255',
        ];
    }
}
