<?php


namespace App\Services;


use App\DTO\SalaryDTO;
use App\Models\SalaryLog;
use Illuminate\Support\Carbon;

class CalculateService
{

    public function calculate(SalaryDTO $dto):array
    {
        return [
            'salary' => $dto->salary,
            'ipn' => $this->getIpn($dto->salary, $dto->hasTaxDeduction),
            'opv' => $this->getOpv($dto->salary),
            'osms' => $this->getOsms($dto->salary),
            'vosms' => $this->getVosms($dto->salary),
            'so' => $this->getSo($dto->salary),
            'final_salary' => $this->getFinalSalary($dto->salary,
                $dto->hasTaxDeduction,
                $dto->isPensioner,
                $dto->isInvalid,
                $dto->invalidGroup)
        ];
    }

    public function save(SalaryDTO $dto)
    {
        $data = $this->calculate($dto);
        SalaryLog::create([
            'employee_id' => $dto->employeeId,
            'salary' => $data['final_salary'],
            'date' => Carbon::createFromDate($dto->year, $dto->month, 1)->format('Y-m-d')
        ]);
        return $data;
    }

    public function getFinalSalary(float $salary,
                                   bool $hasTaxDeduction,
                                   bool $isPensioner,
                                   bool $isInvalid,
                                   ?int $invalidGroup): float
    {
        if ($isPensioner && $isInvalid) {
            return $salary;
        }

        if ($isPensioner) {
            return $salary - $this->getIpn($salary, $hasTaxDeduction);
        }

        if ($isInvalid) {
            $finalSalary = $salary - $this->getSo($salary);

            if ($invalidGroup === 3) {
                $finalSalary -= $this->getOpv($salary);
            }

            if ($salary > 882 * config('common.MRP')) {
                $finalSalary -= $this->getIpn($salary, $hasTaxDeduction);
            }
            return $finalSalary;
        }

        return $salary - $this->getIpn($salary, $hasTaxDeduction) -
            $this->getSo($salary) - $this->getOpv($salary) -
            $this->getOsms($salary) - $this->getVosms($salary);
    }

    public function getIpn(float $salary, bool $hasTaxDeduction): float
    {
        $ipn = $salary - $this->getOpv($salary) - $this->getVosms($salary);

        if ($hasTaxDeduction) {
            $ipn -= config('common.MZP');
        }

        if ($salary < 25 * config('common.MRP')) {
            $correction = $salary - $this->getOpv($salary) - $this->getVosms($salary);

            if ($hasTaxDeduction) {
                $correction -= config('common.MZP');
            }

            $correction *= 0.9;
        }

        if (isset($correction)) {
            $ipn -= $correction;
        }

        return $ipn * 0.1;
    }

    public function getOpv(float $salary): float
    {
        return $salary * 0.1;
    }

    public function getVosms(float $salary): float
    {
        return $salary * 0.02;
    }

    public function getOsms(float $salary): float
    {
        return $salary * 0.02;
    }

    public function getSo(float $salary): float
    {
        return ($salary - $this->getOpv($salary)) * 0.035;
    }


}
