<?php

namespace App\Services;

use App\Models\PatientRecord;
use Carbon\Carbon;

class PatientService
{
    public function getTodayPatients()
    {
        return PatientRecord::whereBetween('created_at', [
            Carbon::today(),
            Carbon::tomorrow(),
        ])->get();
    }
}
