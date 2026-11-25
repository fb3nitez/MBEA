<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseNutritionPrescription extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'prescription_date' => 'date',
        'form_data' => 'array',
        'patient_age' => 'integer',
    ];

    public function patientRecord(): BelongsTo
    {
        return $this->belongsTo(PatientRecord::class);
    }

    public function prescriber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prescribed_by');
    }
}
