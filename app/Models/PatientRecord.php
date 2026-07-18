<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PatientRecord extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'birthday' => 'date',
    ];

    protected static function booted()
    {
        static::created(function ($patient) {
            $patient->patient_id = date('Y') . '-' . str_pad($patient->id, 6, '0', STR_PAD_LEFT);
            $patient->saveQuietly();
        });
    }

    public function medicalHistory(): HasOne
    {
        return $this->hasOne(MedicalHistory::class);
    }

    public function psychiatricHistory(): HasOne
    {
        return $this->hasOne(PsychiatricHistory::class);
    }

    public function lifestyleAssessment(): HasOne
    {
        return $this->hasOne(LifestyleAssessment::class);
    }
}
