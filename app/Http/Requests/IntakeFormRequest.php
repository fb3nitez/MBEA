<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IntakeFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Step 1 - Patient Information
            'name' => 'required|string|max:255',
            'birthday' => 'required|date|before:today',
            'sex' => 'required|string|in:male,female',
            'chiefComplaint' => 'required|string',
            'gender' => 'nullable|string|max:255',
            'maritalStatus' => 'nullable|string|max:255',

            // Step 2 - Medical History (conditional validation handled on frontend)
            'pmhAutoimmuneSpecify' => 'nullable|string|max:255|required_if:pmhAutoimmune,true',
            'pmhCancerSpecify' => 'nullable|string|max:255|required_if:pmhCancer,true',
            'pmhOtherSpecify' => 'nullable|string|max:255|required_if:pmhOther,true',

            // Step 3 - Psychiatric History
            'diagnosedMH' => 'nullable|in:yes,no',
            'diagnosisSpecify' => 'nullable|string|max:255|required_if:diagnosedMH,yes',
            'hospitalized' => 'nullable|in:yes,no',
            'hospTimes' => 'nullable|integer|min:0|required_if:hospitalized,yes',
            'hospWhen' => 'nullable|string|max:255|required_if:hospitalized,yes',

            // Step 4 - Lifestyle Assessment
            'healthScore' => 'required|integer|min:1|max:10',
            'sleepHours' => 'nullable|integer|min:0|max:24',
            'tiredFrequency' => 'nullable|string|max:255',
            'weightPerception' => 'nullable|string|max:255',
            'fastFood' => 'nullable|string|max:255',
            'fruitsVeg' => 'nullable|string|max:255',
            'exerciseFreq' => 'nullable|string|max:255',
            'phqLittleInterest' => 'nullable|string|max:255',
            'phqFeelingDown' => 'nullable|string|max:255',
            'phqTroubleSleeping' => 'nullable|string|max:255',
            'phqFeelingTired' => 'nullable|string|max:255',
            'phqPoorAppetite' => 'nullable|string|max:255',
            'phqFeelingBad' => 'nullable|string|max:255',
            'phqTroubleConcentrating' => 'nullable|string|max:255',
            'phqMovingSlow' => 'nullable|string|max:255',
            'phqThoughtsHurting' => 'nullable|string|max:255',

            // Conditional substance validation
            'subNicotineAmount' => 'nullable|string|max:255|required_if:subNicotine,true',
            'subAlcoholAmount' => 'nullable|string|max:255|required_if:subAlcohol,true',
            'subRecreationalAmount' => 'nullable|string|max:255|required_if:subRecreational,true',
            'subMarijuanaAmount' => 'nullable|string|max:255|required_if:subMarijuana,true',
            'subScreentimeAmount' => 'nullable|string|max:255|required_if:subScreentime,true',
            'subGamblingAmount' => 'nullable|string|max:255|required_if:subGambling,true',
            'subOthersSpecify' => 'nullable|string|max:255|required_if:subOthers,true',
        ];
    }
}
