<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'control_no' => 'required|string',
            'name' => 'required|string|unique:users,name',
            'password' => 'required|string|min:6',
            'office_id' => 'required|exists:offices,id',
            'role_id' => 'required|exists:Roles,id',
            'designation' => 'required|string',
        ];
    }
}
