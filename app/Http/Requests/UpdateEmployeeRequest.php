<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            'department_id' => ['sometimes', 'required', 'integer', 'exists:departments,id'],
            'employee_number' => ['sometimes', 'required', 'string', 'max:30', Rule::unique('employees', 'employee_number')->ignore($employee)],
            'first_name' => ['sometimes', 'required', 'string', 'max:80'],
            'last_name' => ['sometimes', 'required', 'string', 'max:80'],
            'email' => ['sometimes', 'required', 'email', Rule::unique('employees', 'email')->ignore($employee)],
            'position' => ['sometimes', 'required', 'string', 'max:100'],
            'employment_status' => ['sometimes', 'required', Rule::in(['Active', 'Inactive'])],
        ];
    }
}
