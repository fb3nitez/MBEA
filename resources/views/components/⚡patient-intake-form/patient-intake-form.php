<?php

use Livewire\Component;
use Livewire\Attributes\Validate;

use App\Models\PatientRecord;
use App\Models\MedicalHistory;
use App\Models\PsychiatricHistory;
use App\Models\LifestyleAssessment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

new class extends Component
{
    // ============================================================
    // STEP MANAGEMENT
    // ============================================================
    public $currentStep = 1;
    public $totalSteps = 5;

    // ============================================================
    // PATIENT INFORMATION (Step 1)
    // ============================================================
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|date|before:today')]
    public $birthday = '';

    #[Validate('nullable|string|max:255')]
    public $religion = '';

    #[Validate('required|string|in:male,female')]
    public $sex = '';

    #[Validate('nullable|string|in:Straight,Gay,Lesbian,Bisexual,Transgender,Queer,Other')]
    public $gender = 'Straight';

    #[Validate('nullable|string|in:single,married,annulled,widowed,separated')]
    public $maritalStatus = '';

    #[Validate('nullable|string|max:255')]
    public $yearLevel = '';

    #[Validate('nullable|string|max:255')]
    public $course = '';

    #[Validate('nullable|string|max:255')]
    public $occupation = '';

    #[Validate('required|string')]
    public $chiefComplaint = '';

    #[Validate('nullable|string|max:255')]
    public $primaryDiagnosis = '';

    // ============================================================
    // MEDICAL HISTORY (Step 2)
    // ============================================================
    // Personal Medical History - BOOLEAN fields
    #[Validate('boolean')]
    public $pmhHypertension = false;

    #[Validate('boolean')]
    public $pmhStroke = false;

    #[Validate('boolean')]
    public $pmhTuberculosis = false;

    #[Validate('boolean')]
    public $pmhThyroid = false;

    #[Validate('boolean')]
    public $pmhDiabetes = false;

    #[Validate('boolean')]
    public $pmhChronicPain = false;

    #[Validate('boolean')]
    public $pmhAsthma = false;

    #[Validate('boolean')]
    public $pmhEpilepsy = false;

    #[Validate('boolean')]
    public $pmhAutoimmune = false;

    #[Validate('boolean')]
    public $pmhCancer = false;

    #[Validate('boolean')]
    public $pmhOther = false;

    // STRING fields with conditional validation
    #[Validate('nullable|string|max:255|required_if:pmhAutoimmune,true')]
    public $pmhAutoimmuneSpecify = '';

    #[Validate('nullable|string|max:255|required_if:pmhCancer,true')]
    public $pmhCancerSpecify = '';

    #[Validate('nullable|string|max:255|required_if:pmhOther,true')]
    public $pmhOtherSpecify = '';

    #[Validate('nullable|string')]
    public $currentMedications = '';

    // Family Medical History - BOOLEAN fields
    #[Validate('boolean')]
    public $fhHypertension = false;

    #[Validate('boolean')]
    public $fhStroke = false;

    #[Validate('boolean')]
    public $fhDiabetes = false;

    #[Validate('boolean')]
    public $fhSubstance = false;

    #[Validate('boolean')]
    public $fhCancer = false;

    #[Validate('boolean')]
    public $fhPsychiatric = false;

    #[Validate('boolean')]
    public $fhOther = false;

    // Family history STRING fields with conditional validation
    #[Validate('nullable|string|max:255|required_if:fhCancer,true')]
    public $fhCancerType = '';

    #[Validate('nullable|string|max:255|required_if:fhCancer,true')]
    public $fhCancerRelation = '';

    #[Validate('nullable|string|max:255|required_if:fhPsychiatric,true')]
    public $fhPsychiatricType = '';

    #[Validate('nullable|string|max:255|required_if:fhPsychiatric,true')]
    public $fhPsychiatricRelation = '';

    #[Validate('nullable|string|max:255|required_if:fhOther,true')]
    public $fhOtherSpecify = '';

    #[Validate('nullable|string|max:255|required_if:fhOther,true')]
    public $fhOtherRelation = '';

    // ============================================================
    // PSYCHIATRIC HISTORY (Step 3)
    // ============================================================
    #[Validate('nullable|string|in:yes,no')]
    public $diagnosedMH = '';

    #[Validate('nullable|string|max:255|required_if:diagnosedMH,yes')]
    public $diagnosisSpecify = '';

    #[Validate('nullable|string|in:yes,no')]
    public $hospitalized = '';

    #[Validate('nullable|integer|min:0|required_if:hospitalized,yes')]
    public $hospTimes = '';

    #[Validate('nullable|string|max:255|required_if:hospitalized,yes')]
    public $hospWhen = '';

    // Trauma - BOOLEAN flags
    #[Validate('boolean')]
    public $traumaPhysical = false;

    #[Validate('boolean')]
    public $traumaEmotional = false;

    #[Validate('boolean')]
    public $traumaSexual = false;

    #[Validate('boolean')]
    public $traumaNeglect = false;

    // Physical trauma - BOOLEAN fields
    #[Validate('boolean')]
    public $tpChild = false;

    #[Validate('boolean')]
    public $tpAdult = false;

    #[Validate('boolean')]
    public $tpOngoing = false;

    #[Validate('boolean')]
    public $tpPast = false;

    #[Validate('nullable|string|max:1000|required_if:traumaPhysical,true')]
    public $tpDetails = '';

    // Emotional trauma - BOOLEAN fields
    #[Validate('boolean')]
    public $teChild = false;

    #[Validate('boolean')]
    public $teAdult = false;

    #[Validate('boolean')]
    public $teOngoing = false;

    #[Validate('boolean')]
    public $tePast = false;

    #[Validate('nullable|string|max:1000|required_if:traumaEmotional,true')]
    public $teDetails = '';

    // Sexual trauma - BOOLEAN fields
    #[Validate('boolean')]
    public $tsChild = false;

    #[Validate('boolean')]
    public $tsAdult = false;

    #[Validate('boolean')]
    public $tsOngoing = false;

    #[Validate('boolean')]
    public $tsPast = false;

    #[Validate('nullable|string|max:1000|required_if:traumaSexual,true')]
    public $tsDetails = '';

    // Neglect - BOOLEAN fields
    #[Validate('boolean')]
    public $tnChild = false;

    #[Validate('boolean')]
    public $tnAdult = false;

    #[Validate('boolean')]
    public $tnOngoing = false;

    #[Validate('boolean')]
    public $tnPast = false;

    #[Validate('nullable|string|max:1000|required_if:traumaNeglect,true')]
    public $tnDetails = '';

    // ============================================================
    // LIFESTYLE ASSESSMENT (Step 4)
    // ============================================================
    #[Validate('required|integer|min:1|max:10')]
    public $healthScore = 5;

    #[Validate('nullable|integer|min:0|max:24')]
    public $sleepHours = '';

    #[Validate('nullable|string|in:Always,Often,Sometimes,Rarely,Never')]
    public $tiredFrequency = '';

    #[Validate('nullable|string|in:Underweight,Normal,Overweight,Obese')]
    public $weightPerception = '';

    #[Validate('nullable|string|in:Daily,Weekly,Monthly,Rarely,Never')]
    public $fastFood = '';

    #[Validate('nullable|string|in:0-1,2-3,4-5,6+')]
    public $fruitsVeg = '';

    #[Validate('nullable|string|in:Daily,Weekly,Monthly,Rarely,Never')]
    public $exerciseFreq = '';

    // PHQ-9 - STRING fields with specific values
    #[Validate('nullable|string|in:Not at all,Several days,More than half the days,Nearly every day')]
    public $phqLittleInterest = '';

    #[Validate('nullable|string|in:Not at all,Several days,More than half the days,Nearly every day')]
    public $phqFeelingDown = '';

    #[Validate('nullable|string|in:Not at all,Several days,More than half the days,Nearly every day')]
    public $phqTroubleSleeping = '';

    #[Validate('nullable|string|in:Not at all,Several days,More than half the days,Nearly every day')]
    public $phqFeelingTired = '';

    #[Validate('nullable|string|in:Not at all,Several days,More than half the days,Nearly every day')]
    public $phqPoorAppetite = '';

    #[Validate('nullable|string|in:Not at all,Several days,More than half the days,Nearly every day')]
    public $phqFeelingBad = '';

    #[Validate('nullable|string|in:Not at all,Several days,More than half the days,Nearly every day')]
    public $phqTroubleConcentrating = '';

    #[Validate('nullable|string|in:Not at all,Several days,More than half the days,Nearly every day')]
    public $phqMovingSlow = '';

    #[Validate('nullable|string|in:Not at all,Several days,More than half the days,Nearly every day')]
    public $phqThoughtsHurting = '';

    // Substances - BOOLEAN fields
    #[Validate('boolean')]
    public $subNicotine = false;

    #[Validate('nullable|string|max:255|required_if:subNicotine,true')]
    public $subNicotineAmount = '';

    #[Validate('nullable|integer|min:0|max:10|required_if:subNicotine,true')]
    public $subNicotineConcern = 0;

    #[Validate('boolean')]
    public $subAlcohol = false;

    #[Validate('nullable|string|max:255|required_if:subAlcohol,true')]
    public $subAlcoholAmount = '';

    #[Validate('nullable|integer|min:0|max:10|required_if:subAlcohol,true')]
    public $subAlcoholConcern = 0;

    #[Validate('boolean')]
    public $subRecreational = false;

    #[Validate('nullable|string|max:255|required_if:subRecreational,true')]
    public $subRecreationalAmount = '';

    #[Validate('nullable|integer|min:0|max:10|required_if:subRecreational,true')]
    public $subRecreationalConcern = 0;

    #[Validate('boolean')]
    public $subMarijuana = false;

    #[Validate('nullable|string|max:255|required_if:subMarijuana,true')]
    public $subMarijuanaAmount = '';

    #[Validate('nullable|integer|min:0|max:10|required_if:subMarijuana,true')]
    public $subMarijuanaConcern = 0;

    #[Validate('boolean')]
    public $subScreentime = false;

    #[Validate('nullable|string|max:255|required_if:subScreentime,true')]
    public $subScreentimeAmount = '';

    #[Validate('nullable|integer|min:0|max:10|required_if:subScreentime,true')]
    public $subScreentimeConcern = 0;

    #[Validate('boolean')]
    public $subGambling = false;

    #[Validate('nullable|string|max:255|required_if:subGambling,true')]
    public $subGamblingAmount = '';

    #[Validate('nullable|integer|min:0|max:10|required_if:subGambling,true')]
    public $subGamblingConcern = 0;

    #[Validate('boolean')]
    public $subOthers = false;

    #[Validate('nullable|string|max:255|required_if:subOthers,true')]
    public $subOthersSpecify = '';

    #[Validate('nullable|integer|min:0|max:10|required_if:subOthers,true')]
    public $subOthersConcern = 0;

    #[Validate('nullable|string|max:5000')]
    public $lifestyleMotivation = '';

    #[Validate('nullable|string|in:High,Moderate,Low,Very Low')]
    public $motivationLevel = '';

    // ============================================================
    // RESERVED LIVEWIRE PROPERTIES TO EXCLUDE FROM SESSION
    // ============================================================
    private function getReservedProperties()
    {
        return [
            'attributes',
            'id',
            'redirectTo',
            'listeners',
            'queryString',
            'computed',
            'precomputed',
            'lockedProperties',
            'errorBag',
            'rules',
            'messages',
            'validationAttributes',
        ];
    }

    // ============================================================
    // MOUNT & INITIALIZATION
    // ============================================================
    public function mount()
    {
        // Load from session if exists
        if (session()->has('intake_form')) {
            $data = session('intake_form');
            $reserved = $this->getReservedProperties();

            foreach ($data as $key => $value) {
                // Skip reserved Livewire properties
                if (in_array($key, $reserved)) {
                    continue;
                }

                if (property_exists($this, $key)) {
                    // Ensure boolean values are properly cast
                    if (str_starts_with($key, 'pmh') ||
                        str_starts_with($key, 'fh') ||
                        str_starts_with($key, 'trauma') ||
                        str_starts_with($key, 'tp') ||
                        str_starts_with($key, 'te') ||
                        str_starts_with($key, 'ts') ||
                        str_starts_with($key, 'tn') ||
                        str_starts_with($key, 'sub')) {
                        $this->$key = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    } else {
                        $this->$key = $value;
                    }
                }
            }
        }

        // Load step from URL
        $step = request()->query('step', 1);
        $this->currentStep = min(max((int)$step, 1), $this->totalSteps);
    }

    // ============================================================
    // PROGRESS PERSISTENCE
    // ============================================================
    private function saveProgress()
    {
        $data = [];
        $reserved = $this->getReservedProperties();

        foreach (get_object_vars($this) as $key => $value) {
            // Skip reserved Livewire properties
            if (in_array($key, $reserved)) {
                continue;
            }

            $data[$key] = $value;
        }

        session(['intake_form' => $data]);
    }

    // ============================================================
    // NAVIGATION
    // ============================================================
    public function setStep($step)
    {
        $step = min(max($step, 1), $this->totalSteps);

        // Validate current step before leaving
        if ($step > $this->currentStep) {
            $this->validateCurrentStep();
        }

        $this->currentStep = $step;
        $this->saveProgress();

        // Update URL
        $this->dispatch('update-url', step: $this->currentStep);

        if ($this->currentStep === $this->totalSteps) {
            $this->dispatch('render-summary');
        }
    }

    public function nextStep()
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->validateCurrentStep();
            $this->currentStep++;
            $this->saveProgress();
            $this->dispatch('update-url', step: $this->currentStep);

            if ($this->currentStep === $this->totalSteps) {
                $this->dispatch('render-summary');
            }
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->saveProgress();
            $this->dispatch('update-url', step: $this->currentStep);
        }
    }

    public function goToStep($step)
    {
        $this->setStep($step);
    }

    // ============================================================
    // VALIDATION
    // ============================================================
    private function validateCurrentStep()
    {
        $rules = $this->getStepRules($this->currentStep);
        if (!empty($rules)) {
            $this->validate($rules);
        }
    }

    private function getStepRules($step)
    {
        return match($step) {
            1 => [
                'name' => 'required|string|max:255',
                'birthday' => 'required|date|before:today',
                'sex' => 'required|string|in:male,female',
                'chiefComplaint' => 'required|string',
                'gender' => 'nullable|string|in:Straight,Gay,Lesbian,Bisexual,Transgender,Queer,Other',
                'maritalStatus' => 'nullable|string|in:single,married,annulled,widowed,separated',
            ],
            2 => [
                // Personal medical history - booleans
                'pmhHypertension' => 'boolean',
                'pmhStroke' => 'boolean',
                'pmhTuberculosis' => 'boolean',
                'pmhThyroid' => 'boolean',
                'pmhDiabetes' => 'boolean',
                'pmhChronicPain' => 'boolean',
                'pmhAsthma' => 'boolean',
                'pmhEpilepsy' => 'boolean',
                'pmhAutoimmune' => 'boolean',
                'pmhCancer' => 'boolean',
                'pmhOther' => 'boolean',

                // Conditional required fields
                'pmhAutoimmuneSpecify' => 'nullable|string|max:255|required_if:pmhAutoimmune,true',
                'pmhCancerSpecify' => 'nullable|string|max:255|required_if:pmhCancer,true',
                'pmhOtherSpecify' => 'nullable|string|max:255|required_if:pmhOther,true',
                'currentMedications' => 'nullable|string',

                // Family history - booleans
                'fhHypertension' => 'boolean',
                'fhStroke' => 'boolean',
                'fhDiabetes' => 'boolean',
                'fhSubstance' => 'boolean',
                'fhCancer' => 'boolean',
                'fhPsychiatric' => 'boolean',
                'fhOther' => 'boolean',

                // Conditional required fields
                'fhCancerType' => 'nullable|string|max:255|required_if:fhCancer,true',
                'fhCancerRelation' => 'nullable|string|max:255|required_if:fhCancer,true',
                'fhPsychiatricType' => 'nullable|string|max:255|required_if:fhPsychiatric,true',
                'fhPsychiatricRelation' => 'nullable|string|max:255|required_if:fhPsychiatric,true',
                'fhOtherSpecify' => 'nullable|string|max:255|required_if:fhOther,true',
                'fhOtherRelation' => 'nullable|string|max:255|required_if:fhOther,true',
            ],
            3 => [
                'diagnosedMH' => 'nullable|string|in:yes,no',
                'diagnosisSpecify' => 'nullable|string|max:255|required_if:diagnosedMH,yes',
                'hospitalized' => 'nullable|string|in:yes,no',
                'hospTimes' => 'nullable|integer|min:0|required_if:hospitalized,yes',
                'hospWhen' => 'nullable|string|max:255|required_if:hospitalized,yes',

                // Trauma booleans
                'traumaPhysical' => 'boolean',
                'traumaEmotional' => 'boolean',
                'traumaSexual' => 'boolean',
                'traumaNeglect' => 'boolean',

                // Physical trauma details
                'tpChild' => 'boolean',
                'tpAdult' => 'boolean',
                'tpOngoing' => 'boolean',
                'tpPast' => 'boolean',
                'tpDetails' => 'nullable|string|max:1000|required_if:traumaPhysical,true',

                // Emotional trauma details
                'teChild' => 'boolean',
                'teAdult' => 'boolean',
                'teOngoing' => 'boolean',
                'tePast' => 'boolean',
                'teDetails' => 'nullable|string|max:1000|required_if:traumaEmotional,true',

                // Sexual trauma details
                'tsChild' => 'boolean',
                'tsAdult' => 'boolean',
                'tsOngoing' => 'boolean',
                'tsPast' => 'boolean',
                'tsDetails' => 'nullable|string|max:1000|required_if:traumaSexual,true',

                // Neglect details
                'tnChild' => 'boolean',
                'tnAdult' => 'boolean',
                'tnOngoing' => 'boolean',
                'tnPast' => 'boolean',
                'tnDetails' => 'nullable|string|max:1000|required_if:traumaNeglect,true',
            ],
            4 => [
                'healthScore' => 'required|integer|min:1|max:10',
                'sleepHours' => 'nullable|integer|min:0|max:24',
                'tiredFrequency' => 'nullable|string|in:Always,Often,Sometimes,Rarely,Never',
                'weightPerception' => 'nullable|string|in:Underweight,Normal,Overweight,Obese',
                'fastFood' => 'nullable|string|in:Daily,Weekly,Monthly,Rarely,Never',
                'fruitsVeg' => 'nullable|string|in:0-1,2-3,4-5,6+',
                'exerciseFreq' => 'nullable|string|in:Daily,Weekly,Monthly,Rarely,Never',

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

                // Substances - booleans
                'subNicotine' => 'boolean',
                'subNicotineAmount' => 'nullable|string|max:255|required_if:subNicotine,true',
                'subNicotineConcern' => 'nullable|integer|min:0|max:10|required_if:subNicotine,true',

                'subAlcohol' => 'boolean',
                'subAlcoholAmount' => 'nullable|string|max:255|required_if:subAlcohol,true',
                'subAlcoholConcern' => 'nullable|integer|min:0|max:10|required_if:subAlcohol,true',

                'subRecreational' => 'boolean',
                'subRecreationalAmount' => 'nullable|string|max:255|required_if:subRecreational,true',
                'subRecreationalConcern' => 'nullable|integer|min:0|max:10|required_if:subRecreational,true',

                'subMarijuana' => 'boolean',
                'subMarijuanaAmount' => 'nullable|string|max:255|required_if:subMarijuana,true',
                'subMarijuanaConcern' => 'nullable|integer|min:0|max:10|required_if:subMarijuana,true',

                'subScreentime' => 'boolean',
                'subScreentimeAmount' => 'nullable|string|max:255|required_if:subScreentime,true',
                'subScreentimeConcern' => 'nullable|integer|min:0|max:10|required_if:subScreentime,true',

                'subGambling' => 'boolean',
                'subGamblingAmount' => 'nullable|string|max:255|required_if:subGambling,true',
                'subGamblingConcern' => 'nullable|integer|min:0|max:10|required_if:subGambling,true',

                'subOthers' => 'boolean',
                'subOthersSpecify' => 'nullable|string|max:255|required_if:subOthers,true',
                'subOthersConcern' => 'nullable|integer|min:0|max:10|required_if:subOthers,true',

                'lifestyleMotivation' => 'nullable|string|max:5000',
                'motivationLevel' => 'nullable|string|in:High,Moderate,Low,Very Low',
            ],
            default => [],
        };
    }

    public function validateAll()
    {
        $rules = [];
        for ($i = 1; $i <= $this->totalSteps; $i++) {
            $rules = array_merge($rules, $this->getStepRules($i));
        }
        if (!empty($rules)) {
            $this->validate($rules);
        }
    }

    // ============================================================
    // ADDITIONAL HELPER FOR TYPE VALIDATION
    // ============================================================
    private function validateDataType($field, $value)
    {
        // Check if field is boolean based on naming convention
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
            'subScreentime', 'subGambling', 'subOthers'
        ];

        if (in_array($field, $booleanFields) && !is_bool($value)) {
            return false;
        }

        return true;
    }

    // ============================================================
    // SUMMARY DATA
    // ============================================================
    public function getSummaryData()
    {
        return [
            'patient' => [
                'name' => $this->name,
                'age' => $this->calculateAge(),
                'sex' => $this->sex,
                'chiefComplaint' => $this->chiefComplaint,
            ],
            'medical' => [
                'conditions' => $this->getSelectedMedicalConditions(),
                'medications' => $this->currentMedications,
            ],
            'psychiatric' => [
                'diagnosed' => $this->diagnosedMH,
                'diagnosis' => $this->diagnosisSpecify,
                'hospitalized' => $this->hospitalized,
            ],
            'lifestyle' => [
                'healthScore' => $this->healthScore,
                'sleepHours' => $this->sleepHours,
                'exerciseFreq' => $this->exerciseFreq,
                'motivationLevel' => $this->motivationLevel,
            ],
        ];
    }

    private function getSelectedMedicalConditions()
    {
        $conditions = [];
        $map = [
            'pmhHypertension' => 'Hypertension',
            'pmhStroke' => 'Stroke or TIA',
            'pmhTuberculosis' => 'Tuberculosis',
            'pmhThyroid' => 'Thyroid Disorders',
            'pmhDiabetes' => 'Diabetes Mellitus',
            'pmhChronicPain' => 'Chronic Pain / Fibromyalgia',
            'pmhAsthma' => 'Bronchial Asthma',
            'pmhEpilepsy' => 'Epilepsy / Seizure Disorder',
            'pmhAutoimmune' => 'Autoimmune Disease',
            'pmhCancer' => 'Cancer',
            'pmhOther' => 'Other',
        ];

        foreach ($map as $property => $label) {
            if ($this->$property) {
                $conditions[] = $label;
            }
        }

        return $conditions;
    }

    private function calculateAge()
    {
        if (empty($this->birthday)) return '';

        $today = new \DateTime();
        $dob = new \DateTime($this->birthday);
        $age = $today->diff($dob)->y;

        return $age > 0 ? $age . ' years old' : '';
    }

    public function startOver()
    {
        $this->reset();
        $this->setStep(1);
    }

    // ============================================================
    // FORM SUBMISSION
    // ============================================================
    public function submit()
    {
        $this->validateAll();

        try {
            // Begin transaction
            \DB::beginTransaction();

            // 1. Create Patient Record
            $patientRecord = PatientRecord::create([
                'fullname' => $this->name,
                'birthday' => $this->birthday,
                'religion' => $this->religion,
                'sex' => $this->sex,
                'gender' => $this->gender,
                'marital_status' => $this->maritalStatus,
                'student_year_level' => $this->yearLevel,
                'course' => $this->course,
                'occupation' => $this->occupation,
                'cheif_complaint' => $this->chiefComplaint, // Note: typo in migration
                'primary_diagnosis' => $this->primaryDiagnosis,
            ]);

            // 2. Create Medical History
            MedicalHistory::create([
                'patient_record_id' => $patientRecord->id,

                // Personal Medical History
                'hypertension' => $this->pmhHypertension,
                'stroke_tia' => $this->pmhStroke,
                'diabetes' => $this->pmhDiabetes,
                'bronchial_asthma' => $this->pmhAsthma,
                'tuberculosis' => $this->pmhTuberculosis,
                'thyroid_disorders' => $this->pmhThyroid,
                'chronic_pain_fibromyalgia' => $this->pmhChronicPain,
                'epilepsy_seizure' => $this->pmhEpilepsy,

                'autoimmune_disease' => $this->pmhAutoimmune,
                'autoimmune_specify' => $this->pmhAutoimmuneSpecify,

                'cancer' => $this->pmhCancer,
                'cancer_specify' => $this->pmhCancerSpecify,

                'other_medical' => $this->pmhOther,
                'other_medical_specify' => $this->pmhOtherSpecify,

                'current_medications' => $this->currentMedications,

                // Family History
                'family_hypertension' => $this->fhHypertension,
                'family_stroke' => $this->fhStroke,
                'family_diabetes' => $this->fhDiabetes,

                'family_cancer' => $this->fhCancer,
                'family_cancer_type' => $this->fhCancerType,
                'family_cancer_relation' => $this->fhCancerRelation,

                'family_psychiatric_disorder' => $this->fhPsychiatric,
                'family_psychiatric_relation' => $this->fhPsychiatricRelation,

                'family_substance_use' => $this->fhSubstance,

                'family_other' => $this->fhOther,
                'family_other_specify' => $this->fhOtherSpecify,
                'family_other_relation' => $this->fhOtherRelation,
            ]);

            // 3. Create Psychiatric History
            PsychiatricHistory::create([
                'patient_record_id' => $patientRecord->id,

                // Past psychiatric diagnosis
                'diagnosed_mental_condition' => $this->diagnosedMH === 'yes',
                'mental_condition' => $this->diagnosisSpecify,

                'psychiatric_hospitalized' => $this->hospitalized === 'yes',
                'hospitalization_count' => $this->hospTimes ? (int) $this->hospTimes : null,
                'hospitalization_when' => $this->hospWhen,

                // Physical abuse
                'physical_abuse' => $this->traumaPhysical,
                'physical_child' => $this->tpChild,
                'physical_adult' => $this->tpAdult,
                'physical_ongoing' => $this->tpOngoing,
                'physical_past' => $this->tpPast,
                'physical_notes' => $this->tpDetails,

                // Emotional abuse
                'emotional_abuse' => $this->traumaEmotional,
                'emotional_child' => $this->teChild,
                'emotional_adult' => $this->teAdult,
                'emotional_ongoing' => $this->teOngoing,
                'emotional_past' => $this->tePast,
                'emotional_notes' => $this->teDetails,

                // Sexual abuse
                'sexual_abuse' => $this->traumaSexual,
                'sexual_child' => $this->tsChild,
                'sexual_adult' => $this->tsAdult,
                'sexual_ongoing' => $this->tsOngoing,
                'sexual_past' => $this->tsPast,
                'sexual_notes' => $this->tsDetails,

                // Neglect
                'neglect' => $this->traumaNeglect,
                'neglect_child' => $this->tnChild,
                'neglect_adult' => $this->tnAdult,
                'neglect_ongoing' => $this->tnOngoing,
                'neglect_past' => $this->tnPast,
                'neglect_notes' => $this->tnDetails,
            ]);

            // 4. Create Lifestyle Assessment
            LifestyleAssessment::create([
                'patient_record_id' => $patientRecord->id,

                'health_score' => $this->healthScore,
                'sleep_hours' => $this->sleepHours ? (int) $this->sleepHours : null,
                'tired_frequency' => $this->tiredFrequency,

                'weight_perception' => $this->weightPerception,
                'fast_food_frequency' => $this->fastFood,
                'fruits_veg_servings' => $this->fruitsVeg,

                'exercise_frequency' => $this->exerciseFreq,

                // PHQ-9
                'phq_little_interest' => $this->phqLittleInterest,
                'phq_feeling_down' => $this->phqFeelingDown,
                'phq_trouble_sleeping' => $this->phqTroubleSleeping,
                'phq_feeling_tired' => $this->phqFeelingTired,
                'phq_poor_appetite' => $this->phqPoorAppetite,
                'phq_feeling_bad' => $this->phqFeelingBad,
                'phq_trouble_concentrating' => $this->phqTroubleConcentrating,
                'phq_moving_slow' => $this->phqMovingSlow,
                'phq_thoughts_hurting' => $this->phqThoughtsHurting,

                // Substances
                'sub_nicotine' => $this->subNicotine,
                'sub_nicotine_amount' => $this->subNicotineAmount,
                'sub_nicotine_concern' => $this->subNicotineConcern,

                'sub_alcohol' => $this->subAlcohol,
                'sub_alcohol_amount' => $this->subAlcoholAmount,
                'sub_alcohol_concern' => $this->subAlcoholConcern,

                'sub_recreational' => $this->subRecreational,
                'sub_recreational_amount' => $this->subRecreationalAmount,
                'sub_recreational_concern' => $this->subRecreationalConcern,

                'sub_marijuana' => $this->subMarijuana,
                'sub_marijuana_amount' => $this->subMarijuanaAmount,
                'sub_marijuana_concern' => $this->subMarijuanaConcern,

                'sub_screentime' => $this->subScreentime,
                'sub_screentime_amount' => $this->subScreentimeAmount,
                'sub_screentime_concern' => $this->subScreentimeConcern,

                'sub_gambling' => $this->subGambling,
                'sub_gambling_amount' => $this->subGamblingAmount,
                'sub_gambling_concern' => $this->subGamblingConcern,

                'sub_others' => $this->subOthers,
                'sub_others_specify' => $this->subOthersSpecify,
                'sub_others_concern' => $this->subOthersConcern,

                'lifestyle_motivation' => $this->lifestyleMotivation,
                'motivation_level' => $this->motivationLevel,
            ]);

            // Commit transaction
            \DB::commit();

            // Clear session
            session()->forget('intake_form');

            // Flash success message
            session()->flash('success', 'Thank you! Your information has been submitted successfully. Please wait to be called for your consultation.');

            // Redirect
            return redirect()->to('/');

        } catch (\Exception $e) {
            // Rollback transaction on error
            \DB::rollBack();

            // Log the error (optional)
            \Log::error('Patient intake submission failed: ' . $e->getMessage());

            // Flash error message
            session()->flash('error', 'An error occurred while submitting your information. Please try again.');

            // Stay on the same page with error
            return null;
        }
    }

    // ============================================================
    // RENDER
    // ============================================================
    public function render()
    {
        $progress = round(($this->currentStep / $this->totalSteps) * 100);

        $summaryData = null;
        if ($this->currentStep === $this->totalSteps) {
            $summaryData = $this->getSummaryData();
        }

        return $this->view([
            'progress' => $progress,
            'summaryData' => $summaryData,
        ]);
    }
}
