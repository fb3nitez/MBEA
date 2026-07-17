<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PatientRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
    ];

    /**
     * A patient record has one medical history intake.
     */
    public function medicalHistory(): HasOne
    {
        return $this->hasOne(MedicalHistory::class);
    }

    /**
     * A patient record has one psychiatric history intake.
     */
    public function psychiatricHistory(): HasOne
    {
        return $this->hasOne(PsychiatricHistory::class);
    }

    /**
     * A patient record has one lifestyle assessment intake.
     */
    public function lifestyleAssessment(): HasOne
    {
        return $this->hasOne(LifestyleAssessment::class);
    }
}
