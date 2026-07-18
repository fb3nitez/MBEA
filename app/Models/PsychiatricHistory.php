<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychiatricHistory extends Model
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
        'diagnosed_mental_condition' => 'boolean',
        'psychiatric_hospitalized' => 'boolean',
        'hospitalization_count' => 'integer',

        'physical_abuse' => 'boolean',
        'physical_child' => 'boolean',
        'physical_adult' => 'boolean',
        'physical_ongoing' => 'boolean',
        'physical_past' => 'boolean',

        'emotional_abuse' => 'boolean',
        'emotional_child' => 'boolean',
        'emotional_adult' => 'boolean',
        'emotional_ongoing' => 'boolean',
        'emotional_past' => 'boolean',

        'sexual_abuse' => 'boolean',
        'sexual_child' => 'boolean',
        'sexual_adult' => 'boolean',
        'sexual_ongoing' => 'boolean',
        'sexual_past' => 'boolean',

        'neglect' => 'boolean',
        'neglect_child' => 'boolean',
        'neglect_adult' => 'boolean',
        'neglect_ongoing' => 'boolean',
        'neglect_past' => 'boolean',
    ];

    /**
     * The patient record this psychiatric history belongs to.
     */
    public function patientRecord(): BelongsTo
    {
        return $this->belongsTo(PatientRecord::class);
    }
}
