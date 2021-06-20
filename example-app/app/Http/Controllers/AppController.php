<?php

namespace App\Http\Controllers;


use App\DTO\SalaryDTO;
use App\Http\Requests\FirstRequest;
use App\Services\CalculateService;
use Illuminate\Http\Request;

class AppController extends Controller
{

    protected CalculateService $calculateService;

    public function __construct(CalculateService $calculateService)
    {
        $this->calculateService = $calculateService;
    }

    public function calculate(FirstRequest $request)
    {
        $dto = SalaryDTO::fromRequest($request);
        return response()->json($this->calculateService->calculate($dto));
    }

    public function save(FirstRequest $request)
    {
        $dto = SalaryDTO::fromRequest($request);
        return response()->json($this->calculateService->save($dto));
    }
}
