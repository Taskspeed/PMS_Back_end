<?php

namespace App\Http\Requests\Spms;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOpcrRequest extends FormRequest
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
             'office_opcr_id'   => 'required|array',
            'office_opcr_id.*' => 'required|exists:office_opcrs,id',
            'status'    => 'required|string',
            'remarks'   => 'nullable|string',
        ];
    }
}
