<?php

namespace App\Http\Controllers;

use App\Http\Requests\IntakeFormRequest;
use App\Services\IntakeFormService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class IntakeFormController extends Controller
{
    public function __construct(
        private IntakeFormService $service
    ) {}

    public function create()
    {
        return view('intake-form');
    }

    public function store(IntakeFormRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $patient = $this->service->submit($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your information has been submitted successfully. Please wait to be called for your consultation.',
                'patient_id' => $patient->id,
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->fullname,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Patient intake submission failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your information. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
