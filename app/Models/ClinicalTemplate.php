<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalTemplate extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
        'sort_order' => 'integer',
        'last_used_at' => 'datetime',
        'usage_count' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'tag' => $this->tag,
            'tagClass' => $this->tag_class,
            'desc' => $this->description,
            'payload' => $this->payload ?? [],
            'sort_order' => $this->sort_order,
            'meds' => $this->payload['meds'] ?? [],
            'diag' => $this->payload['diag'] ?? null,
            'tests' => $this->payload['tests'] ?? [],
            'lifestyle' => $this->payload['lifestyle'] ?? [],
            'created_by' => $this->created_by,
            'last_used_at' => $this->last_used_at?->toIso8601String(),
            'usage_count' => $this->usage_count ?? 0,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
