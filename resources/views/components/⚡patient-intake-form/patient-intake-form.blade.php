<div>
    <div class="min-h-screen bg-base-200 text-base-content">
        <main class="mx-auto w-full max-w-5xl px-5 pb-10 pt-2">
            <div class="card-body gap-6">
                <div class="text-center">
                    <h1 class="text-3xl font-bold">Patient Intake Form</h1>
                    <p class="mt-2 text-base-content/80">Please complete all sections before your consultation</p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert alert-error">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-bold">Please fix the following errors:</h3>
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Success Message -->
                @if(session()->has('success'))
                    <div class="alert alert-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <section class="space-y-2">
                    <div class="flex justify-between text-sm text-base-content/80">
                        <p>Step <span id="step-label">{{ $currentStep }}</span> of <span id="max-form-step">{{ $totalSteps }}</span></p>
                        <p class="text-right"><span id="percent-label">{{ $progress }}%</span> Complete</p>
                    </div>
                    <progress id="progress-fill" class="progress w-full" value="{{ $progress }}" max="100"></progress>
                </section>

                <section class="card bg-base-100 border border-base-content/10 shadow-lg p-10 space-y-5">
                    <div id="step-1" class="step-panel {{ $currentStep !== 1 ? 'hidden' : '' }}">
                        <x-forms.patient-information />
                    </div>

                    <div id="step-2" class="step-panel {{ $currentStep !== 2 ? 'hidden' : '' }}">
                        <x-forms.medical-history class="mt-5" />
                    </div>

                    <div id="step-3" class="step-panel {{ $currentStep !== 3 ? 'hidden' : '' }}">
                        <x-forms.psychiatric-history class="mt-5" />
                    </div>

                    <div id="step-4" class="step-panel {{ $currentStep !== 4 ? 'hidden' : '' }}">
                        <x-forms.lifestyle-assessment class="mt-5" />
                    </div>

                    <div id="step-5" class="step-panel {{ $currentStep !== 5 ? 'hidden' : '' }}">
                        <x-forms.review-step :summaryData="$summaryData ?? null" class="mt-5" />
                    </div>

                    <!-- Navigation -->
                    <div class="flex flex-col gap-3 border-t border-base-content/10 pt-6 sm:flex-row sm:items-center sm:justify-between">
                        @if($currentStep > 1)
                            <button type="button" wire:click="prevStep" class="btn btn-outline" id="btn-back">
                                Back
                            </button>
                        @endif

                        <div class="ml-auto flex w-full gap-3 sm:w-auto sm:justify-end">
                            @if($currentStep < $totalSteps)
                                <button type="button" wire:click="nextStep" class="btn btn-primary" id="btn-next">
                                    Next
                                </button>
                            @endif

                            @if($currentStep === $totalSteps)
                                <button type="button" wire:click="submit" class="btn btn-success" id="btn-submit" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Submit</span>
                                    <span wire:loading>
                                        <span class="loading loading-spinner loading-sm"></span>
                                        Submitting...
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>
                </section>

                <p class="pt-2 text-center text-sm text-base-content/60">Your information is confidential and protected under medical privacy laws.</p>
            </div>
        </main>
    </div>
</div>
