<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalHistory extends Model
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
        'hypertension' => 'boolean',
        'stroke_tia' => 'boolean',
        'diabetes' => 'boolean',
        'bronchial_asthma' => 'boolean',
        'tuberculosis' => 'boolean',
        'thyroid_disorders' => 'boolean',
        'chronic_pain_fibromyalgia' => 'boolean',
        'epilepsy_seizure' => 'boolean',
        'autoimmune_disease' => 'boolean',
        'cancer' => 'boolean',
        'other_medical' => 'boolean',
        'family_hypertension' => 'boolean',
        'family_stroke' => 'boolean',
        'family_diabetes' => 'boolean',
        'family_cancer' => 'boolean',
        'family_psychiatric_disorder' => 'boolean',
        'family_substance_use' => 'boolean',
        'family_other' => 'boolean',
    ];

    /**
     * The patient record this medical history belongs to.
     */
    public function patientRecord(): BelongsTo
    {
        return $this->belongsTo(PatientRecord::class);
    }
}
