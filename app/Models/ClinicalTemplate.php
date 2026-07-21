<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicalTemplate extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
        'sort_order' => 'integer',
    ];

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
        ];
    }
}
