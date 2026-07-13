<?php

namespace App\Http\Requests\Spms;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitWorkPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
          protected function prepareForValidation(): void
    {
        if ($this->has('status')) {
            $this->merge([
                'status' => ucwords(strtolower($this->status)),
            ]);
        }
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
            'status' => 'required|string',
            'remarks'          => ['nullable', 'string', 'required_if:status,Returned'],

        ];
    }
}
