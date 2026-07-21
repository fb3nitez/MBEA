<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachingNote extends Model
{
    protected $guarded = ['id'];

    public function patientRecord(): BelongsTo
    {
        return $this->belongsTo(PatientRecord::class);
    }

    public function lifeCoach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'life_coach_id');
    }
}
