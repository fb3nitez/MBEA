<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientInterventionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'psychiatric_therapy_medication' => ['nullable', 'string', 'max:10000'],
            'lifestyle_interventions' => ['nullable', 'string', 'max:10000'],
            'substance_use_rehabilitation' => ['nullable', 'string', 'max:10000'],
            'spiritual_counseling' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
