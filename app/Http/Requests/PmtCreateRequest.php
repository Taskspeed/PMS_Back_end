<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PmtCreateRequest extends FormRequest
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
               'controlNo'          => 'required|string',
            'name'               => 'required|string',
            'designation'        => 'required|string',
            'role_id'            => 'required|exists:roles,id',
            'office_id'          => 'required|exists:offices,id',
            'password'           => 'required|string|min:6',
            'username'           => 'required|string|min:3|unique:users,username',
            'active'             => 'required|boolean',
            'office_id_assign'   => 'required|array',
            'office_id_assign.*' => 'required|exists:offices,id',
            'pmt_type'           => 'nullable|string',
            'prefix'           => 'nullable|string',
            'suffix'           => 'nullable|string',
        ];
    }
}
