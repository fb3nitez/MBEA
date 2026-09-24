<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpiritualIntakeRequest extends FormRequest
{
    private const TEXT_FIELDS = [
        'religious_background_childhood_other_text',
        'religious_background_adolescent_other_text',
        'religious_background_current_other_text',
        'new_age_other_text',
        'additional_spiritual_issues_other_text',
        'spiritual_explain_hypnosis',
        'spiritual_guidance_question',
        'spiritual_voices_question',
        'spiritual_unusual_experiences_question',
        'spiritual_prayer_question',
        'spiritual_ritual_worship_question',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach ($this->all() as $field => $value) {
            if (! is_string($field) || in_array($field, self::TEXT_FIELDS, true)) {
                continue;
            }

            if (is_string($value)) {
                $normalized[$field] = in_array(strtolower($value), ['on', 'true', '1', 'yes'], true);
            }
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
    }

    public function rules(): array
    {
        $rules = array_fill_keys(self::TEXT_FIELDS, ['nullable', 'string', 'max:5000']);

        foreach ($this->all() as $field => $value) {
            if (! is_string($field) || in_array($field, self::TEXT_FIELDS, true)) {
                continue;
            }

            $rules[$field] = ['required', 'boolean'];
        }

        return $rules;
    }
}
