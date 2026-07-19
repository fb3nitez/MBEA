<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
            $patient->updateQuietly([
                'patient_id' => sprintf('%s-%06d', now()->year, $patient->id),
            ]);
        });
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->birthday)->age
        );
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
