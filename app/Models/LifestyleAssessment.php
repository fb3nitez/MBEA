<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LifestyleAssessment extends Model
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
        'health_score' => 'integer',
        'sleep_hours' => 'integer',

        'sub_nicotine' => 'boolean',
        'sub_nicotine_concern' => 'integer',

        'sub_alcohol' => 'boolean',
        'sub_alcohol_concern' => 'integer',

        'sub_recreational' => 'boolean',
        'sub_recreational_concern' => 'integer',

        'sub_marijuana' => 'boolean',
        'sub_marijuana_concern' => 'integer',

        'sub_screentime' => 'boolean',
        'sub_screentime_concern' => 'integer',

        'sub_gambling' => 'boolean',
        'sub_gambling_concern' => 'integer',

        'sub_others' => 'boolean',
        'sub_others_concern' => 'integer',
    ];

    /**
     * The patient record this lifestyle assessment belongs to.
     */
    public function patientRecord(): BelongsTo
    {
        return $this->belongsTo(PatientRecord::class);
    }
}
