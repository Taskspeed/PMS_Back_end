<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackerRequest extends FormRequest
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

            'office_id' => 'required|exists:offices,id',
            // 'office_name' => 'required|string',
            'year' => 'required|integer',
            'semester' => 'required|string',
            'date' => 'required|date',
            'status' => 'required|string',
            'remarks' => 'required|string'
        ];
    }
}
