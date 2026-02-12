<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class opcrRequest extends FormRequest
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
            '*.performance_standard_id' => 'required|exists:performance_standards,id',
            '*.budget' => 'required',
            '*.accountable' => 'required',
            '*.accomplishment' => 'required',
        ];
    }
}
