<div class="space-y-6">
    <div class="text-center">
        <h2 class="card-title justify-center text-2xl">Review Your Information</h2>
        <p class="text-base-content/70">Please review your responses before submitting</p>
    </div>

    <div class="alert alert-info items-start">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <div>
            <h3 class="font-semibold">Almost Done</h3>
            <p class="text-sm">Review each section below. You can edit any section by clicking the Edit button.</p>
        </div>
    </div>

    <!-- Patient Information Card -->
    <div class="card bg-base-100 border border-base-content/10 shadow-sm">
        <div class="card-body gap-4">
            <div class="flex items-center justify-between gap-4">
                <button type="button" onclick="toggleReviewCard('patient-review')" class="flex items-center gap-2 hover:opacity-70">
                    <svg class="w-5 h-5 transition-transform" id="patient-review-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    <h3 class="card-title text-lg">Patient Information</h3>
                </button>
                <button type="button" onclick="goToStep(1)" class="btn btn-outline btn-sm">Edit</button>
            </div>
            <div id="patient-review" class="space-y-2">
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Name</span>
                    <span id="summary-name" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Birthday</span>
                    <span id="summary-birthday" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Sex</span>
                    <span id="summary-sex" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Religion</span>
                    <span id="summary-religion" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Gender</span>
                    <span id="summary-gender" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Marital Status</span>
                    <span id="summary-marital" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Year Level</span>
                    <span id="summary-year" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Course</span>
                    <span id="summary-course" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Occupation</span>
                    <span id="summary-occupation" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Chief Complaint</span>
                    <span id="summary-chief" class="text-base-content/80 text-right">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1">
                    <span class="font-medium">Primary Diagnosis</span>
                    <span id="summary-diagnosis" class="text-base-content/80 text-right">Not provided</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical History Card -->
    <div class="card bg-base-100 border border-base-content/10 shadow-sm">
        <div class="card-body gap-4">
            <div class="flex items-center justify-between gap-4">
                <button type="button" onclick="toggleReviewCard('medical-review')" class="flex items-center gap-2 hover:opacity-70">
                    <svg class="w-5 h-5 transition-transform" id="medical-review-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    <h3 class="card-title text-lg">Medical History</h3>
                </button>
                <button type="button" onclick="goToStep(2)" class="btn btn-outline btn-sm">Edit</button>
            </div>
            <div id="medical-review" class="space-y-2">
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Personal Medical History</span>
                    <span id="summary-pmh" class="text-base-content/80">None selected</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Autoimmune Details</span>
                    <span id="summary-autoimmune" class="text-base-content/80">N/A</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Cancer Details</span>
                    <span id="summary-cancer" class="text-base-content/80">N/A</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Other Medical Conditions</span>
                    <span id="summary-other-medical" class="text-base-content/80">N/A</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Current Medications</span>
                    <span id="summary-medications" class="text-base-content/80 text-right">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Family Medical History</span>
                    <span id="summary-fh" class="text-base-content/80">None selected</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Family Cancer</span>
                    <span id="summary-fh-cancer" class="text-base-content/80">N/A</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Family Psychiatric</span>
                    <span id="summary-fh-psych" class="text-base-content/80">N/A</span>
                </div>
                <div class="summary-row flex justify-between py-1">
                    <span class="font-medium">Family Other</span>
                    <span id="summary-fh-other" class="text-base-content/80">N/A</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Psychiatric History Card -->
    <div class="card bg-base-100 border border-base-content/10 shadow-sm">
        <div class="card-body gap-4">
            <div class="flex items-center justify-between gap-4">
                <button type="button" onclick="toggleReviewCard('psychiatric-review')" class="flex items-center gap-2 hover:opacity-70">
                    <svg class="w-5 h-5 transition-transform" id="psychiatric-review-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    <h3 class="card-title text-lg">Personal History</h3>
                </button>
                <button type="button" onclick="goToStep(3)" class="btn btn-outline btn-sm">Edit</button>
            </div>
            <div id="psychiatric-review" class="space-y-2">
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Diagnosed with mental health condition</span>
                    <span id="summary-diagnosed" class="text-base-content/80">Not answered</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Diagnosis</span>
                    <span id="summary-diagnosis-specify" class="text-base-content/80">N/A</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Previous hospitalization</span>
                    <span id="summary-hospitalized" class="text-base-content/80">Not answered</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Hospitalization Count</span>
                    <span id="summary-hosp-times" class="text-base-content/80">N/A</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Hospitalization When</span>
                    <span id="summary-hosp-when" class="text-base-content/80">N/A</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Physical Abuse</span>
                    <span id="summary-trauma-physical" class="text-base-content/80">No</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Emotional Abuse</span>
                    <span id="summary-trauma-emotional" class="text-base-content/80">No</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Sexual Abuse</span>
                    <span id="summary-trauma-sexual" class="text-base-content/80">No</span>
                </div>
                <div class="summary-row flex justify-between py-1">
                    <span class="font-medium">Neglect</span>
                    <span id="summary-trauma-neglect" class="text-base-content/80">No</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lifestyle Assessment Card - Collapsed by default -->
    <div class="card bg-base-100 border border-base-content/10 shadow-sm">
        <div class="card-body gap-4">
            <div class="flex items-center justify-between gap-4">
                <button type="button" onclick="toggleReviewCard('lifestyle-review')" class="flex items-center gap-2 hover:opacity-70">
                    <svg class="w-5 h-5 transition-transform rotate-180" id="lifestyle-review-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    <h3 class="card-title text-lg">Lifestyle Related Behaviors</h3>
                </button>
                <button type="button" onclick="goToStep(4)" class="btn btn-outline btn-sm">Edit</button>
            </div>
            <div id="lifestyle-review" class="space-y-2 hidden">
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Health Score</span>
                    <span id="summary-health-score" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Sleep Hours</span>
                    <span id="summary-sleep" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Tired Frequency</span>
                    <span id="summary-tired" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Weight Perception</span>
                    <span id="summary-weight" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Fast Food Frequency</span>
                    <span id="summary-fastfood" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Fruits & Vegetables</span>
                    <span id="summary-fruitsveg" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Exercise Frequency</span>
                    <span id="summary-exercise" class="text-base-content/80">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">PHQ-9 Scores</span>
                    <span id="summary-phq" class="text-base-content/80">Not answered</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Substance Use</span>
                    <span id="summary-substances" class="text-base-content/80">None reported</span>
                </div>
                <div class="summary-row flex justify-between py-1 border-b border-base-content/5">
                    <span class="font-medium">Motivation Areas</span>
                    <span id="summary-motivation-text" class="text-base-content/80 text-right">Not provided</span>
                </div>
                <div class="summary-row flex justify-between py-1">
                    <span class="font-medium">Motivation Level</span>
                    <span id="summary-motivation" class="text-base-content/80">Not provided</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-200/50 border border-base-content/10">
        <div class="card-body">
            <h4 class="font-semibold">Privacy &amp; Confidentiality</h4>
            <p class="text-sm text-base-content/70">All information provided is confidential and protected under medical privacy laws. Your data will only be accessed by authorized healthcare professionals involved in your care.</p>
        </div>
    </div>
</div>
