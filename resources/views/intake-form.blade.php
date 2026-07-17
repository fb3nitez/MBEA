<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Intake Form</title>
    @vite(['resources/css/app.css', 'resources/js/intake-form.js'])
</head>

<body>
    <div id="app" class="min-h-screen bg-base-200 text-base-content">
        <main class="mx-auto w-full max-w-5xl px-5 pb-10 pt-2">
            <div class="card-body gap-6">
                <div class="text-center">
                    <h1 class="text-3xl font-bold">Patient Intake Form</h1>
                    <p class="mt-2 text-base-content/80">Please complete all sections before your consultation</p>
                </div>

                <!-- Messages -->
                <div id="error-messages" class="hidden">
                    <div class="alert alert-error">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-bold">Please fix the following errors:</h3>
                            <ul id="error-list" class="list-disc list-inside"></ul>
                        </div>
                    </div>
                </div>

                <div id="success-message" class="hidden">
                    <div class="alert alert-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="success-text"></span>
                    </div>
                </div>

                <!-- Progress -->
                <section class="space-y-2">
                    <div class="flex justify-between text-sm text-base-content/80">
                        <p>Step <span id="step-label">1</span> of <span id="max-form-step">5</span></p>
                        <p class="text-right"><span id="percent-label">20%</span> Complete</p>
                    </div>
                    <progress id="progress-fill" class="progress w-full" value="20" max="100"></progress>
                </section>

                <!-- Form -->
                <form id="intake-form" class="relative card bg-base-100 border border-base-content/10 shadow-lg py-5 px-10 space-y-5">
                    <button type="button" class="btn btn-ghost absolute top-6 right-5" onclick="start_over_dialog.showModal()">
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                    </button>

                    <div id="step-1" class="step-panel">
                        <x-forms.patient-information />
                    </div>

                    <div id="step-2" class="step-panel hidden">
                        <x-forms.medical-history class="mt-5" />
                    </div>

                    <div id="step-3" class="step-panel hidden">
                        <x-forms.psychiatric-history class="mt-5" />
                    </div>

                    <div id="step-4" class="step-panel hidden">
                        <x-forms.lifestyle-assessment class="mt-5" />
                    </div>

                    <div id="step-5" class="step-panel hidden">
                        <x-forms.review-step class="mt-5" />
                    </div>

                    <x-forms.form-navigation />
                </form>

                <p class="pt-2 text-center text-sm text-base-content/60">Your information is confidential and protected under medical privacy laws.</p>
            </div>
        </main>
    </div>

    <x-dialog.start-over-dialog />
</body>
</html>
