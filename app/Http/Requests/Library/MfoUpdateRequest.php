<?php

namespace App\Http\Requests\Library;

use Illuminate\Foundation\Http\FormRequest;

class MfoUpdateRequest extends FormRequest
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
          'office_id' => 'required|exists:offices,id',
          'name' => 'required|string|max:255',
            'f_category_id' => 'required|exists:f_categories,id',
        ];
    }
}
