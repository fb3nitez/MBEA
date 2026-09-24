<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLifestyleAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = array_fill_keys([
            'sub_nicotine', 'sub_alcohol', 'sub_recreational', 'sub_marijuana',
            'sub_screentime', 'sub_gambling', 'sub_others',
        ], ['nullable', 'boolean']);

        $rules['health_score'] = ['nullable', 'integer', 'min:0', 'max:10'];
        $rules['sleep_hours'] = ['nullable', 'integer', 'min:0', 'max:24'];

        foreach ([
            'tired_frequency', 'weight_perception', 'fast_food_frequency',
            'fruits_veg_servings', 'exercise_frequency', 'phq_little_interest',
            'phq_feeling_down', 'phq_trouble_sleeping', 'phq_feeling_tired',
            'phq_poor_appetite', 'phq_feeling_bad', 'phq_trouble_concentrating',
            'phq_moving_slow', 'phq_thoughts_hurting', 'motivation_level',
        ] as $field) {
            $rules[$field] = ['nullable', 'string', 'max:255'];
        }

        foreach ([
            'sub_nicotine_amount', 'sub_alcohol_amount', 'sub_recreational_amount',
            'sub_marijuana_amount', 'sub_screentime_amount', 'sub_gambling_amount',
            'sub_others_specify', 'lifestyle_motivation',
        ] as $field) {
            $rules[$field] = ['nullable', 'string', 'max:5000'];
        }

        foreach ([
            'sub_nicotine_concern', 'sub_alcohol_concern',
            'sub_recreational_concern', 'sub_marijuana_concern',
            'sub_screentime_concern', 'sub_gambling_concern', 'sub_others_concern',
        ] as $field) {
            $rules[$field] = ['nullable', 'integer', 'min:0', 'max:10'];
        }

        return $rules;
    }
}
