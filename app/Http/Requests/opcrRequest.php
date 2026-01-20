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
            //

            'performance_standard_id' => 'required|exists:performance_standards,id',
            'compentency' => 'required|array',
            'budget' => 'required',
            'accountable' => 'required',
            'accomplishment' => 'required',
            'rating_q' =>  ['required', 'numeric', 'min:0', 'max:10'],
            'rating_e' =>  ['required', 'numeric', 'min:0', 'max:10'],
            'rating_t' =>  ['required', 'numeric', 'min:0', 'max:10'],
            'rating_a' =>  ['required', 'numeric', 'min:0', 'max:10'],
            'profiency' => 'required|array',
            'remarks' => 'required',

        ];
    }
}
