@props(['summaryData' => null])

<x-forms.form-container {{ $attributes }}>
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

        <x-forms.summary-card title="Patient Information" :step="1">
            <div id="summary-patient" class="space-y-2">
                @if($summaryData && isset($summaryData['patient']))
                    <div class="summary-row">
                        <span class="label">Name</span>
                        <span class="value">{{ $summaryData['patient']['name'] ?? 'Not provided' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Age</span>
                        <span class="value">{{ $summaryData['patient']['age'] ?? 'Not provided' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Sex</span>
                        <span class="value">{{ $summaryData['patient']['sex'] ?? 'Not provided' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Chief Complaint</span>
                        <span class="value">{{ $summaryData['patient']['chiefComplaint'] ?? 'Not provided' }}</span>
                    </div>
                @else
                    <div class="summary-row">
                        <span class="value text-base-content/50">No data available</span>
                    </div>
                @endif
            </div>
        </x-forms.summary-card>

        <x-forms.summary-card title="Medical History" :step="2">
            <div id="summary-medical" class="space-y-2">
                @if($summaryData && isset($summaryData['medical']))
                    <div class="summary-row">
                        <span class="label">Personal Medical History</span>
                        <div class="badge-row">
                            @forelse($summaryData['medical']['conditions'] ?? [] as $condition)
                                <span class="badge">{{ $condition }}</span>
                            @empty
                                <span class="value">None selected</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="summary-row">
                        <span class="label">Current Medications</span>
                        <span class="value">{{ $summaryData['medical']['medications'] ?? 'Not provided' }}</span>
                    </div>
                @else
                    <div class="summary-row">
                        <span class="value text-base-content/50">No data available</span>
                    </div>
                @endif
            </div>
        </x-forms.summary-card>

        <x-forms.summary-card title="Psychiatric History" :step="3">
            <div id="summary-psychiatric" class="space-y-2">
                @if($summaryData && isset($summaryData['psychiatric']))
                    <div class="summary-row">
                        <span class="label">Diagnosed with mental health condition</span>
                        <span class="value">{{ ucfirst($summaryData['psychiatric']['diagnosed'] ?? 'Not answered') }}</span>
                    </div>
                    @if(($summaryData['psychiatric']['diagnosed'] ?? '') === 'yes')
                        <div class="summary-row">
                            <span class="label">Diagnosis</span>
                            <span class="value">{{ $summaryData['psychiatric']['diagnosis'] ?? 'Not provided' }}</span>
                        </div>
                    @endif
                    <div class="summary-row">
                        <span class="label">Previous hospitalization</span>
                        <span class="value">{{ ucfirst($summaryData['psychiatric']['hospitalized'] ?? 'Not answered') }}</span>
                    </div>
                @else
                    <div class="summary-row">
                        <span class="value text-base-content/50">No data available</span>
                    </div>
                @endif
            </div>
        </x-forms.summary-card>

        <x-forms.summary-card title="Lifestyle Assessment" :step="4">
            <div id="summary-lifestyle" class="space-y-2">
                @if($summaryData && isset($summaryData['lifestyle']))
                    <div class="summary-row">
                        <span class="label">Health Score</span>
                        <span class="value">{{ ($summaryData['lifestyle']['healthScore'] ?? 0) . '/10' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Sleep Hours</span>
                        <span class="value">{{ $summaryData['lifestyle']['sleepHours'] ?? 'Not provided' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Exercise Frequency</span>
                        <span class="value">{{ $summaryData['lifestyle']['exerciseFreq'] ?? 'Not provided' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Motivation Level</span>
                        <span class="value">{{ ucfirst(str_replace('-', ' ', $summaryData['lifestyle']['motivationLevel'] ?? 'Not provided')) }}</span>
                    </div>
                @else
                    <div class="summary-row">
                        <span class="value text-base-content/50">No data available</span>
                    </div>
                @endif
            </div>
        </x-forms.summary-card>

        <div class="card bg-base-200/50 border border-base-content/10">
            <div class="card-body">
                <h4 class="font-semibold">Privacy &amp; Confidentiality</h4>
                <p class="text-sm text-base-content/70">All information provided is confidential and protected under medical privacy laws. Your data will only be accessed by authorized healthcare professionals involved in your care.</p>
            </div>
        </div>
    </div>
</x-forms.form-container>
