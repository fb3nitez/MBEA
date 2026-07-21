<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachingTask extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'due_date' => 'date',
        'is_done' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function patientRecord(): BelongsTo
    {
        return $this->belongsTo(PatientRecord::class);
    }

    public function lifeCoach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'life_coach_id');
    }
}
