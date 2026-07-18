<?php

namespace App\Http\Controllers;


class PsychiatristController extends Controller
{
    public function dashboard()
    {
        return view('psychiatrist.dashboard');
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
