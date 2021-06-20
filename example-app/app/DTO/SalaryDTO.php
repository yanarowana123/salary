<?php

namespace App\DTO;


use Illuminate\Http\Request;
use Spatie\DataTransferObject\DataTransferObject;

class SalaryDTO extends DataTransferObject
{
    public float $salary;

    public int $daysNorm;

    public int $daysWork;

    public bool $hasTaxDeduction;

    public int $year;

    public int $month;

    public bool $isPensioner;

    public bool $isInvalid;

    public ?int $invalidGroup;

    public ?int $employeeId;

    public const DAYS_NORM = 22;

    public static function fromRequest(Request $request): self
    {
        $fields = [
            'salary' => (float)$request->salary,
            'daysNorm' => $request->days_norm ? (int)$request->days_norm : self::DAYS_NORM,
            'daysWork' => (int)$request->days_work,
            'hasTaxDeduction' => (bool)$request->has_tax_deduction,
            'year' => (int)$request->year,
            'month' => (int)$request->month,
            'isPensioner' => (bool)$request->is_pensioner,
            'isInvalid' => (bool)$request->is_invalid,
            'employeeId' => $request->employee_id ? (int)$request->employee_id : null
        ];

        if ((bool)$request->is_invalid) {
            $fields['invalidGroup'] = $request->invalid_group ? (int)$request->invalid_group : null;
        }

        return new self($fields);
    }

}
