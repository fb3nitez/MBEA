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
     * Normalize HTML checkbox values ("on") before boolean validation runs.
     */
    protected function prepareForValidation(): void
    {
        $booleanFields = [
            'pmhHypertension', 'pmhStroke', 'pmhTuberculosis', 'pmhThyroid',
            'pmhDiabetes', 'pmhChronicPain', 'pmhAsthma', 'pmhEpilepsy',
            'pmhAutoimmune', 'pmhCancer', 'pmhOther',
            'fhHypertension', 'fhStroke', 'fhDiabetes', 'fhSubstance',
            'fhCancer', 'fhPsychiatric', 'fhOther',
            'traumaPhysical', 'traumaEmotional', 'traumaSexual', 'traumaNeglect',
            'tpChild', 'tpAdult', 'tpOngoing', 'tpPast',
            'teChild', 'teAdult', 'teOngoing', 'tePast',
            'tsChild', 'tsAdult', 'tsOngoing', 'tsPast',
            'tnChild', 'tnAdult', 'tnOngoing', 'tnPast',
            'subNicotine', 'subAlcohol', 'subRecreational', 'subMarijuana',
            'subScreentime', 'subGambling', 'subOthers',
        ];

        $normalized = [];

        foreach ($booleanFields as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $value = $this->input($field);

            if (is_bool($value)) {
                $normalized[$field] = $value;
                continue;
            }

            if (is_string($value)) {
                $normalized[$field] = in_array(strtolower($value), ['on', 'true', '1', 'yes'], true);
                continue;
            }

            if (is_numeric($value)) {
                $normalized[$field] = (int) $value === 1;
            }
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // ============================================================
            // STEP 1 - PATIENT INFORMATION
            // ============================================================
            'name' => 'required|string|max:255',
            'birthday' => 'required|date|before:today',
            'religion' => 'nullable|string|max:255',
            'sex' => 'required|string|in:male,female',
            'gender' => 'nullable|string|max:255',
            'gender_other' => 'nullable|string|max:255',
            'maritalStatus' => 'required|string|in:single,married,annulled,widowed,separated',
            'yearLevel' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'chiefComplaint' => 'required|string',
            'primaryDiagnosis' => 'nullable|string|max:255',

            // ============================================================
            // STEP 2 - MEDICAL HISTORY
            // ============================================================
            // Personal Medical History - Boolean fields
            'pmhHypertension' => 'nullable|boolean',
            'pmhStroke' => 'nullable|boolean',
            'pmhTuberculosis' => 'nullable|boolean',
            'pmhThyroid' => 'nullable|boolean',
            'pmhDiabetes' => 'nullable|boolean',
            'pmhChronicPain' => 'nullable|boolean',
            'pmhAsthma' => 'nullable|boolean',
            'pmhEpilepsy' => 'nullable|boolean',
            'pmhAutoimmune' => 'nullable|boolean',
            'pmhCancer' => 'nullable|boolean',
            'pmhOther' => 'nullable|boolean',

            // Personal Medical History - Conditional fields
            'pmhAutoimmuneSpecify' => 'nullable|string|max:255|required_if:pmhAutoimmune,true',
            'pmhCancerSpecify' => 'nullable|string|max:255|required_if:pmhCancer,true',
            'pmhOtherSpecify' => 'nullable|string|max:255|required_if:pmhOther,true',
            'currentMedications' => 'nullable|string',

            // Family Medical History - Boolean fields
            'fhHypertension' => 'nullable|boolean',
            'fhStroke' => 'nullable|boolean',
            'fhDiabetes' => 'nullable|boolean',
            'fhSubstance' => 'nullable|boolean',
            'fhCancer' => 'nullable|boolean',
            'fhPsychiatric' => 'nullable|boolean',
            'fhOther' => 'nullable|boolean',

            // Family Medical History - Conditional fields
            'fhCancerType' => 'nullable|string|max:255|required_if:fhCancer,true',
            'fhCancerRelation' => 'nullable|string|max:255|required_if:fhCancer,true',
            'fhPsychiatricType' => 'nullable|string|max:255|required_if:fhPsychiatric,true',
            'fhPsychiatricRelation' => 'nullable|string|max:255|required_if:fhPsychiatric,true',
            'fhOtherSpecify' => 'nullable|string|max:255|required_if:fhOther,true',
            'fhOtherRelation' => 'nullable|string|max:255|required_if:fhOther,true',

            // ============================================================
            // STEP 3 - PSYCHIATRIC HISTORY
            // ============================================================
            // Mental Health Diagnosis
            'diagnosedMH' => 'nullable|in:yes,no',
            'diagnosisSpecify' => 'nullable|string|max:255|required_if:diagnosedMH,yes',
            'hospitalized' => 'nullable|in:yes,no',
            'hospTimes' => 'nullable|integer|min:0|required_if:hospitalized,yes',
            'hospWhen' => 'nullable|string|max:255|required_if:hospitalized,yes',

            // Trauma - Boolean flags
            'traumaPhysical' => 'nullable|boolean',
            'traumaEmotional' => 'nullable|boolean',
            'traumaSexual' => 'nullable|boolean',
            'traumaNeglect' => 'nullable|boolean',

            // Physical Trauma
            'tpChild' => 'nullable|boolean',
            'tpAdult' => 'nullable|boolean',
            'tpOngoing' => 'nullable|boolean',
            'tpPast' => 'nullable|boolean',
            'tpDetails' => 'nullable|string|max:1000',

            // Emotional Trauma
            'teChild' => 'nullable|boolean',
            'teAdult' => 'nullable|boolean',
            'teOngoing' => 'nullable|boolean',
            'tePast' => 'nullable|boolean',
            'teDetails' => 'nullable|string|max:1000',

            // Sexual Trauma
            'tsChild' => 'nullable|boolean',
            'tsAdult' => 'nullable|boolean',
            'tsOngoing' => 'nullable|boolean',
            'tsPast' => 'nullable|boolean',
            'tsDetails' => 'nullable|string|max:1000',

            // Neglect
            'tnChild' => 'nullable|boolean',
            'tnAdult' => 'nullable|boolean',
            'tnOngoing' => 'nullable|boolean',
            'tnPast' => 'nullable|boolean',
            'tnDetails' => 'nullable|string|max:1000',

            // ============================================================
            // STEP 4 - LIFESTYLE ASSESSMENT
            // ============================================================
            // General Health
            'healthScore' => 'required|integer|min:1|max:10',
            'sleepHours' => 'nullable|integer|min:0|max:24',
            'tiredFrequency' => 'nullable|string|in:Always,Often,Sometimes,Rarely,Never',
            'weightPerception' => 'nullable|string|in:Underweight,Normal,Overweight,Obese',
            'fastFood' => 'nullable|string|in:Never,Monthly,Weekly,Often,Daily',
            'fruitsVeg' => 'nullable|string|in:0-1,2-3,4-5,6+',
            'exerciseFreq' => 'nullable|string|in:0,1-2,3-4,5-6,7',

            // PHQ-9
            'phqLittleInterest' => 'nullable|string|in:Not at all,Several days,More than half the days,Nearly every day',
            'phqFeelingDown' => 'nullable|string|in:Not at all,Several days,More than half the days,Nearly every day',
            'phqTroubleSleeping' => 'nullable|string|in:Not at all,Several days,More than half the days,Nearly every day',
            'phqFeelingTired' => 'nullable|string|in:Not at all,Several days,More than half the days,Nearly every day',
            'phqPoorAppetite' => 'nullable|string|in:Not at all,Several days,More than half the days,Nearly every day',
            'phqFeelingBad' => 'nullable|string|in:Not at all,Several days,More than half the days,Nearly every day',
            'phqTroubleConcentrating' => 'nullable|string|in:Not at all,Several days,More than half the days,Nearly every day',
            'phqMovingSlow' => 'nullable|string|in:Not at all,Several days,More than half the days,Nearly every day',
            'phqThoughtsHurting' => 'nullable|string|in:Not at all,Several days,More than half the days,Nearly every day',

            // Substances - Boolean flags
            'subNicotine' => 'nullable|boolean',
            'subAlcohol' => 'nullable|boolean',
            'subRecreational' => 'nullable|boolean',
            'subMarijuana' => 'nullable|boolean',
            'subScreentime' => 'nullable|boolean',
            'subGambling' => 'nullable|boolean',
            'subOthers' => 'nullable|boolean',

            // Substances - Conditional fields
            'subNicotineAmount' => 'nullable|string|max:255',
            'subNicotineConcern' => 'nullable|integer|min:0|max:10',
            'subAlcoholAmount' => 'nullable|string|max:255',
            'subAlcoholConcern' => 'nullable|integer|min:0|max:10',
            'subRecreationalAmount' => 'nullable|string|max:255',
            'subRecreationalConcern' => 'nullable|integer|min:0|max:10',
            'subMarijuanaAmount' => 'nullable|string|max:255',
            'subMarijuanaConcern' => 'nullable|integer|min:0|max:10',
            'subScreentimeAmount' => 'nullable|string|max:255',
            'subScreentimeConcern' => 'nullable|integer|min:0|max:10',
            'subGamblingAmount' => 'nullable|string|max:255',
            'subGamblingConcern' => 'nullable|integer|min:0|max:10',
            'subOthersSpecify' => 'nullable|string|max:255',
            'subOthersConcern' => 'nullable|integer|min:0|max:10',

            // Motivation
            'lifestyleMotivation' => 'nullable|string|max:5000',
            'motivationLevel' => 'nullable|string|in:Very Low,Low,Moderate,High',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Patient Information
            'name.required' => 'Full name is required.',
            'name.max' => 'Full name cannot exceed 255 characters.',
            'birthday.required' => 'Birthday is required.',
            'birthday.date' => 'Please enter a valid date.',
            'birthday.before' => 'Birthday must be a date before today.',
            'sex.required' => 'Sex is required.',
            'sex.in' => 'Please select a valid sex option.',
            'chiefComplaint.required' => 'Chief complaint is required.',
            'maritalStatus.required' => 'Marital status is required.',
            'maritalStatus.in' => 'Please select a valid marital status.',

            // Medical History
            'pmhAutoimmuneSpecify.required_if' => 'Please specify the autoimmune disease.',
            'pmhCancerSpecify.required_if' => 'Please specify the type of cancer.',
            'pmhOtherSpecify.required_if' => 'Please specify the other medical condition.',
            'fhCancerType.required_if' => 'Please specify the type of cancer in family history.',
            'fhCancerRelation.required_if' => 'Please specify the relation for family cancer history.',
            'fhPsychiatricType.required_if' => 'Please specify the psychiatric disorder in family history.',
            'fhPsychiatricRelation.required_if' => 'Please specify the relation for family personal history.',
            'fhOtherSpecify.required_if' => 'Please specify the other condition in family history.',
            'fhOtherRelation.required_if' => 'Please specify the relation for family other history.',

            // Psychiatric History
            'diagnosisSpecify.required_if' => 'Please specify your mental health diagnosis.',
            'hospTimes.required_if' => 'Please specify the number of hospitalizations.',
            'hospTimes.integer' => 'Hospitalization count must be a number.',
            'hospTimes.min' => 'Hospitalization count must be at least 0.',
            'hospWhen.required_if' => 'Please specify when you were hospitalized.',

            // Lifestyle Assessment
            'healthScore.required' => 'Health score is required.',
            'healthScore.integer' => 'Health score must be a number.',
            'healthScore.min' => 'Health score must be at least 1.',
            'healthScore.max' => 'Health score cannot exceed 10.',
            'sleepHours.integer' => 'Sleep hours must be a number.',
            'sleepHours.min' => 'Sleep hours cannot be negative.',
            'sleepHours.max' => 'Sleep hours cannot exceed 24.',
            'tiredFrequency.in' => 'Please select a valid tired frequency option.',
            'weightPerception.in' => 'Please select a valid weight perception option.',
            'fastFood.in' => 'Please select a valid fast food frequency option.',
            'fruitsVeg.in' => 'Please select a valid fruits and vegetables option.',
            'exerciseFreq.in' => 'Please select a valid exercise frequency option.',
            'phqLittleInterest.in' => 'Please select a valid PHQ-9 response.',
            'phqFeelingDown.in' => 'Please select a valid PHQ-9 response.',
            'phqTroubleSleeping.in' => 'Please select a valid PHQ-9 response.',
            'phqFeelingTired.in' => 'Please select a valid PHQ-9 response.',
            'phqPoorAppetite.in' => 'Please select a valid PHQ-9 response.',
            'phqFeelingBad.in' => 'Please select a valid PHQ-9 response.',
            'phqTroubleConcentrating.in' => 'Please select a valid PHQ-9 response.',
            'phqMovingSlow.in' => 'Please select a valid PHQ-9 response.',
            'phqThoughtsHurting.in' => 'Please select a valid PHQ-9 response.',

            // Substances
            'subNicotineAmount.required_if' => 'Please specify the nicotine amount.',
            'subNicotineConcern.required_if' => 'Please specify your level of concern for nicotine.',
            'subNicotineConcern.integer' => 'Concern level must be a number.',
            'subNicotineConcern.min' => 'Concern level must be at least 0.',
            'subNicotineConcern.max' => 'Concern level cannot exceed 10.',
            'subAlcoholAmount.required_if' => 'Please specify the alcohol amount.',
            'subAlcoholConcern.required_if' => 'Please specify your level of concern for alcohol.',
            'subAlcoholConcern.integer' => 'Concern level must be a number.',
            'subAlcoholConcern.min' => 'Concern level must be at least 0.',
            'subAlcoholConcern.max' => 'Concern level cannot exceed 10.',
            'subRecreationalAmount.required_if' => 'Please specify the recreational drugs amount.',
            'subRecreationalConcern.required_if' => 'Please specify your level of concern for recreational drugs.',
            'subRecreationalConcern.integer' => 'Concern level must be a number.',
            'subRecreationalConcern.min' => 'Concern level must be at least 0.',
            'subRecreationalConcern.max' => 'Concern level cannot exceed 10.',
            'subMarijuanaAmount.required_if' => 'Please specify the marijuana amount.',
            'subMarijuanaConcern.required_if' => 'Please specify your level of concern for marijuana.',
            'subMarijuanaConcern.integer' => 'Concern level must be a number.',
            'subMarijuanaConcern.min' => 'Concern level must be at least 0.',
            'subMarijuanaConcern.max' => 'Concern level cannot exceed 10.',
            'subScreentimeAmount.required_if' => 'Please specify the screen time amount.',
            'subScreentimeConcern.required_if' => 'Please specify your level of concern for screen time.',
            'subScreentimeConcern.integer' => 'Concern level must be a number.',
            'subScreentimeConcern.min' => 'Concern level must be at least 0.',
            'subScreentimeConcern.max' => 'Concern level cannot exceed 10.',
            'subGamblingAmount.required_if' => 'Please specify the gambling amount.',
            'subGamblingConcern.required_if' => 'Please specify your level of concern for gambling.',
            'subGamblingConcern.integer' => 'Concern level must be a number.',
            'subGamblingConcern.min' => 'Concern level must be at least 0.',
            'subGamblingConcern.max' => 'Concern level cannot exceed 10.',
            'subOthersSpecify.required_if' => 'Please specify the other substance.',
            'subOthersConcern.required_if' => 'Please specify your level of concern for other substances.',
            'subOthersConcern.integer' => 'Concern level must be a number.',
            'subOthersConcern.min' => 'Concern level must be at least 0.',
            'subOthersConcern.max' => 'Concern level cannot exceed 10.',

            // Motivation
            'motivationLevel.in' => 'Please select a valid motivation level.',
        ];
    }
}
