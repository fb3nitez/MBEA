<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePsychiatricHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = array_fill_keys([
            'diagnosed_mental_condition', 'psychiatric_hospitalized',
            'physical_abuse', 'physical_child', 'physical_adult',
            'physical_ongoing', 'physical_past', 'emotional_abuse',
            'emotional_child', 'emotional_adult', 'emotional_ongoing',
            'emotional_past', 'sexual_abuse', 'sexual_child', 'sexual_adult',
            'sexual_ongoing', 'sexual_past', 'neglect', 'neglect_child',
            'neglect_adult', 'neglect_ongoing', 'neglect_past',
        ], ['nullable', 'boolean']);

        $rules['hospitalization_count'] = ['nullable', 'integer', 'min:0'];

        foreach ([
            'mental_condition', 'hospitalization_when', 'physical_notes',
            'emotional_notes', 'sexual_notes', 'neglect_notes',
        ] as $field) {
            $rules[$field] = ['nullable', 'string', 'max:1000'];
        }

        return $rules;
    }
}
