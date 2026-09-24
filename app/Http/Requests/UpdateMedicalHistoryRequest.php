<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $booleanFields = [
            'hypertension', 'stroke_tia', 'diabetes', 'bronchial_asthma',
            'tuberculosis', 'thyroid_disorders', 'chronic_pain_fibromyalgia',
            'epilepsy_seizure', 'autoimmune_disease', 'cancer', 'other_medical',
            'family_hypertension', 'family_stroke', 'family_diabetes',
            'family_cancer', 'family_psychiatric_disorder', 'family_substance_use',
            'family_other',
        ];

        $rules = array_fill_keys($booleanFields, ['nullable', 'boolean']);
        $rules['current_medications'] = ['nullable', 'string', 'max:5000'];

        foreach ([
            'autoimmune_specify', 'cancer_specify', 'other_medical_specify',
            'family_hypertension_relation', 'family_stroke_relation',
            'family_diabetes_relation', 'family_cancer_type',
            'family_cancer_relation', 'family_psychiatric_relation',
            'family_substance_relation', 'family_other_specify',
            'family_other_relation',
        ] as $field) {
            $rules[$field] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }
}
