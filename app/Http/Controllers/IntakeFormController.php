<?php

namespace App\Http\Controllers;

use App\Http\Requests\IntakeFormRequest;
use App\Services\IntakeFormService;

class IntakeFormController extends Controller
{
    public function __construct(
        private IntakeFormService $service
    ) {}

    public function store(IntakeFormRequest $request)
    {
        $patient = $this->service->submit($request->validated());

        return response()->json([
            'success' => true,
            'patient' => $patient,
        ]);
    }
}
