<?php

namespace App\Http\Requests\Library;

use Illuminate\Foundation\Http\FormRequest;

class OutputRequest extends FormRequest
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

            'name' => 'required|string|max:255',
            'f_category_id' => 'required|exists:f_categories,id',
            'office_id' => 'required|exists:offices,id'

        ];
    }
}
