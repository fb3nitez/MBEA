<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationSchedule extends Model
{
    use HasFactory;

    protected $table = 'consultation_schedule';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function patientRecord(): BelongsTo
    {
        return $this->belongsTo(PatientRecord::class);
    }
}
