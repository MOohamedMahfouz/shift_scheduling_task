<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShiftRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('shifts', 'name')],
            'start_time' => ['required', Rule::date()->todayOrAfter()],
            'end_time' => ['required', Rule::date()->after('start_time')],
            'max_resources' => ['required', Rule::numeric()->integer()->greaterThanOrEqualTo(1)],
            'max_employees' => ['required', Rule::numeric()->integer()->greaterThanOrEqualTo(1)],
            'department_id' => ['required', Rule::exists('departments', 'id')],
        ];
    }
}
