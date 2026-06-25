<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAttachmentRatingRequest extends FormRequest
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
          'performance_standard_id' => 'required|integer|exists:performance_standards,id',
          'week_number'             => 'required|integer|min:1|max:5',
          'month'                   => 'required|string',
         'year'                    => 'required|integer',
          'file'                    => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ];
    }
}
