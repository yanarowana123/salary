<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class FirstRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'salary' => 'required|numeric',
            'days_norm' =>'nullable|integer|between:1,31',
            'days_work' => 'required|integer|between:1,31',
            'has_tax_deduction' => 'required|bool',
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
            'is_pensioner' => 'required|bool',
            'is_invalid' => 'required|bool',
            'invalid_group' => 'required_if:is_invalid,==,1|integer|between:1,3',
            'employee_id' => 'nullable|integer',
        ];
    }

}
