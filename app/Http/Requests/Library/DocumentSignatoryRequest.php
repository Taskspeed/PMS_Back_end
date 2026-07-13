<?php

namespace App\Http\Requests\Library;

use Illuminate\Foundation\Http\FormRequest;

class DocumentSignatoryRequest extends FormRequest
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
        'control_no' => 'required|string',
        //performance standard
        'performance_standard_discussed_by_control_no' => 'required|string',
        'performance_standard_approved_by_control_no' => 'required|string',
        //ipcr
        'ipcr_reviewed_by_control_no' => 'required|string',
        'ipcr_approved_by_control_no' => 'required|string',
        'ipcr_assessed_by_control_no' => 'required|string',
        'ipcr_final_rating_by_control_no' => 'required|string',
        //por
        'por_confirmed_control_no' => 'required|string',
        'por_approved_final_rating_control_no'  => 'required|string',
        ];
    }
}
