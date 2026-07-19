<?php

namespace App\Http\Controllers;

use App\Services\PatientService;

class PsychiatristController extends Controller
{
    public function dashboard(PatientService $patientService)
    {
        $patients = $patientService->getTodayPatients();
        return view('psychiatrist.dashboard', [
            'patients' => $patients,
        ]);
    }

    public function patients()
    {
        return view('psychiatrist.patients');
    }

    public function consultations()
    {
        return view('psychiatrist.consultations');
    }

    public function records()
    {
        return view('psychiatrist.records');
    }

    public function lifestyle()
    {
        return view('psychiatrist.lifestyle');
    }

    public function assessments()
    {
        return view('psychiatrist.assessments');
    }

    public function prescriptions()
    {
        return view('psychiatrist.prescriptions');
    }
}
