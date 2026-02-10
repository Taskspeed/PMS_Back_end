<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_period_id' => 'required|exists:target_periods,id',

            'months' => 'required|array|min:1',
            'months.*.month' => 'required|string',

            'months.*.absent' => 'required|array',
            'months.*.absent.week1' => 'nullable|integer|min:0',
            'months.*.absent.week2' => 'nullable|integer|min:0',
            'months.*.absent.week3' => 'nullable|integer|min:0',
            'months.*.absent.week4' => 'nullable|integer|min:0',
            'months.*.absent.week5' => 'nullable|integer|min:0',
            'months.*.absent.total_absent' => 'nullable|integer|min:0',

            'months.*.late' => 'required|array',
            'months.*.late.week1' => 'nullable|integer|min:0',
            'months.*.late.week2' => 'nullable|integer|min:0',
            'months.*.late.week3' => 'nullable|integer|min:0',
            'months.*.late.week4' => 'nullable|integer|min:0',
            'months.*.late.week5' => 'nullable|integer|min:0',
            'months.*.late.total_late' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'target_period_id.required' => 'Target period ID is required',
            'target_period_id.exists' => 'Invalid target period ID',

            'months.required' => 'Months data is required',
            'months.*.month.required' => 'Month name is required for each entry',

            'months.*.absent.required' => 'Absent data is required for each month',
            'months.*.absent.week1.required' => 'Absent week 1 is required',
            'months.*.absent.week2.required' => 'Absent week 2 is required',
            'months.*.absent.week3.required' => 'Absent week 3 is required',
            'months.*.absent.week4.required' => 'Absent week 4 is required',
            'months.*.absent.week5.required' => 'Absent week 5 is required',
            'months.*.absent.total_absent.required' => 'Total absent is required',

            'months.*.late.required' => 'Late data is required for each month',
            'months.*.late.week1.required' => 'Late week 1 is required',
            'months.*.late.week2.required' => 'Late week 2 is required',
            'months.*.late.week3.required' => 'Late week 3 is required',
            'months.*.late.week4.required' => 'Late week 4 is required',
            'months.*.late.week5.required' => 'Late week 5 is required',
            'months.*.late.total_late.required' => 'Total late is required',
        ];
    }
}
