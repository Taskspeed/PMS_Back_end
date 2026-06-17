<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupervisorCreateRequest extends FormRequest
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
            'name' => 'required|string',
            'designation' => 'required|string',
            'role_id'     => 'required|exists:roles,id',  // fixed: 'exist' → 'exists', 'role' → 'roles' (use actual table name)
            'controlNo' => 'required|string',
            'username'    => 'required|string|unique:users,username', // added unique check
            'password' => 'required|string|min:3',
            'active' => 'required|boolean',
            'prefix' =>'nullable|string',
            'suffix' =>'nullable|string'
        ];
    }
}
