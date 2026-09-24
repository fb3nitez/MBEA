<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalTemplateUserState extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_favorite' => 'boolean',
        'last_used_at' => 'datetime',
        'usage_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ClinicalTemplate::class, 'clinical_template_id');
    }
}
