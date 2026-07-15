<?php

use Livewire\Component;
use Livewire\Attributes\Validate;

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

    public $religion = '';

    #[Validate('required|in:male,female')]
    public $sex = '';

    public $gender = 'Straight';
    public $maritalStatus = '';
    public $yearLevel = '';
    public $course = '';
    public $occupation = '';

    #[Validate('required|string')]
    public $chiefComplaint = '';

    public $primaryDiagnosis = '';

    // ============================================================
    // MEDICAL HISTORY (Step 2)
    // ============================================================
    // Personal Medical History
    public $pmhHypertension = false;
    public $pmhStroke = false;
    public $pmhTuberculosis = false;
    public $pmhThyroid = false;
    public $pmhDiabetes = false;
    public $pmhChronicPain = false;
    public $pmhAsthma = false;
    public $pmhEpilepsy = false;
    public $pmhAutoimmune = false;
    public $pmhCancer = false;
    public $pmhOther = false;

    public $pmhAutoimmuneSpecify = '';
    public $pmhCancerSpecify = '';
    public $pmhOtherSpecify = '';
    public $currentMedications = '';

    // Family Medical History
    public $fhHypertension = false;
    public $fhStroke = false;
    public $fhDiabetes = false;
    public $fhSubstance = false;
    public $fhCancer = false;
    public $fhPsychiatric = false;
    public $fhOther = false;

    public $fhCancerType = '';
    public $fhCancerRelation = '';
    public $fhPsychiatricType = '';
    public $fhPsychiatricRelation = '';
    public $fhOtherSpecify = '';
    public $fhOtherRelation = '';

    // ============================================================
    // PSYCHIATRIC HISTORY (Step 3)
    // ============================================================
    public $diagnosedMH = '';
    public $diagnosisSpecify = '';
    public $hospitalized = '';
    public $hospTimes = '';
    public $hospWhen = '';

    // Trauma
    public $traumaPhysical = false;
    public $traumaEmotional = false;
    public $traumaSexual = false;
    public $traumaNeglect = false;

    public $tpChild = false;
    public $tpAdult = false;
    public $tpOngoing = false;
    public $tpPast = false;
    public $tpDetails = '';

    public $teChild = false;
    public $teAdult = false;
    public $teOngoing = false;
    public $tePast = false;
    public $teDetails = '';

    public $tsChild = false;
    public $tsAdult = false;
    public $tsOngoing = false;
    public $tsPast = false;
    public $tsDetails = '';

    public $tnChild = false;
    public $tnAdult = false;
    public $tnOngoing = false;
    public $tnPast = false;
    public $tnDetails = '';

    // ============================================================
    // LIFESTYLE ASSESSMENT (Step 4)
    // ============================================================
    public $healthScore = 5;
    public $sleepHours = '';
    public $tiredFrequency = '';
    public $weightPerception = '';
    public $fastFood = '';
    public $fruitsVeg = '';
    public $exerciseFreq = '';

    // PHQ-9
    public $phqLittleInterest = '';
    public $phqFeelingDown = '';
    public $phqTroubleSleeping = '';
    public $phqFeelingTired = '';
    public $phqPoorAppetite = '';
    public $phqFeelingBad = '';
    public $phqTroubleConcentrating = '';
    public $phqMovingSlow = '';
    public $phqThoughtsHurting = '';

    // Substances
    public $subNicotine = false;
    public $subNicotineAmount = '';
    public $subNicotineConcern = 0;

    public $subAlcohol = false;
    public $subAlcoholAmount = '';
    public $subAlcoholConcern = 0;

    public $subRecreational = false;
    public $subRecreationalAmount = '';
    public $subRecreationalConcern = 0;

    public $subMarijuana = false;
    public $subMarijuanaAmount = '';
    public $subMarijuanaConcern = 0;

    public $subScreentime = false;
    public $subScreentimeAmount = '';
    public $subScreentimeConcern = 0;

    public $subGambling = false;
    public $subGamblingAmount = '';
    public $subGamblingConcern = 0;

    public $subOthers = false;
    public $subOthersSpecify = '';
    public $subOthersConcern = 0;

    public $lifestyleMotivation = '';
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
                    $this->$key = $value;
                }
            }
        }

        // Load step from URL
        $step = request()->query('step', 1);
        $this->currentStep = min(max((int)$step, 1), $this->totalSteps);
    }

    // ============================================================
    // PROGRESS PERSISTENCE (FIXED)
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
                'sex' => 'required|in:male,female',
                'chiefComplaint' => 'required|string',
            ],
            2 => [],
            3 => [],
            4 => [],
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

    // ============================================================
    // FORM SUBMISSION
    // ============================================================
    public function submit()
    {
        $this->validateAll();

        // Here you would save to database
        // Example:
        /*
        $patient = Patient::create([
            'name' => $this->name,
            'birthday' => $this->birthday,
            'religion' => $this->religion,
            'sex' => $this->sex,
            'gender' => $this->gender,
            'marital_status' => $this->maritalStatus,
            'year_level' => $this->yearLevel,
            'course' => $this->course,
            'occupation' => $this->occupation,
            'chief_complaint' => $this->chiefComplaint,
            'primary_diagnosis' => $this->primaryDiagnosis,
            'medical_history' => json_encode($this->getSelectedMedicalConditions()),
            'current_medications' => $this->currentMedications,
            'family_history' => json_encode($this->getFamilyHistory()),
            'psychiatric_history' => json_encode($this->getPsychiatricData()),
            'lifestyle_data' => json_encode($this->getLifestyleData()),
        ]);
        */

        // Clear session
        session()->forget('intake_form');

        // Flash success message
        session()->flash('success', 'Thank you! Your information has been submitted successfully. Please wait to be called for your consultation.');

        // Redirect
        return redirect()->to('/');
    }

    // ============================================================
    // RENDER (UNCHANGED - Keep your original)
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
