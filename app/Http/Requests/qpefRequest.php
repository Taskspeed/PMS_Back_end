<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class qpefRequest extends FormRequest
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
        $isUpdate = $this->isMethod('put');
        return [

            // 'control_no'    => 'required|string',
            // 'quarterly'     => 'required|string',
            // 'year'          => 'required|integer',

            'control_no' => $isUpdate ? 'sometimes|string' : 'required|string',
            'quarterly'  => $isUpdate ? 'sometimes|string' : 'required|string',
            'year'       => $isUpdate ? 'sometimes|integer' : 'required|integer',

            'job_performance' => 'required|array|min:1',
            'job_performance.*.indicators' => 'required|string',
            'job_performance.*.rating' => 'required|integer',
            'job_performance.*.remarks' => 'required|string',

            'competencies_attitude' => 'required|array|min:1',
            'competencies_attitude.*.indicators' => 'required|string',
            'competencies_attitude.*.rating' => 'required|integer',
            'competencies_attitude.*.remarks' => 'required|string',

            'physical_mental' => 'required|array|min:1',
            'physical_mental.*.indicators' => 'required|string',
            'physical_mental.*.rating' => 'required|integer',
            'physical_mental.*.remarks' => 'required|string',


            // These should be nested under recommendation_development
            'recommendation_development' => 'required|array',
            'recommendation_development.for_retention' => 'required|boolean',
            'recommendation_development.for_commendation' => 'required|boolean',
            'recommendation_development.for_improvement' => 'required|boolean',
            'recommendation_development.for_non_renewal' => 'required|boolean',
            'recommendation_development.recommendation' => 'required|string',

            // In your validation rules
            'job_performance.*.id' => 'sometimes|integer|exists:job_performances,id',
            'competencies_attitude.*.id' => 'sometimes|integer|exists:competencies_attitudes,id',
            'physical_mental.*.id' => 'sometimes|integer|exists:physical_mentals,id',


        ];
    }
}
