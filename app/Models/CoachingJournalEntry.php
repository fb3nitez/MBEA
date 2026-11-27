<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachingJournalEntry extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'entry_date' => 'date',
        'content' => 'array',
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
