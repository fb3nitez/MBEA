<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Intake Form</title>
    @vite(['resources/css/app.css', 'resources/js/intake-form.js'])
</head>

<body class="min-h-screen bg-base-200 text-base-content">
    <main class="mx-auto w-full max-w-4xl px-5 pb-10 pt-2">
        <div class="card-body gap-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold">Patient Intake Form</h1>
                <p class="mt-2 text-base-content/80">Please complete all sections before your consultation</p>
            </div>

            <section class="space-y-2">
                <div class="flex justify-between text-sm text-base-content/80">
                    <p>Step <span id="step-label">1</span> of <span id="max-form-step">5</span></p>
                    <p class="text-right"><span id="percent-label">20%</span> Complete</p>
                </div>
                <progress id="progress-fill" class="progress w-full" value="20" max="100"></progress>
            </section>

            <section class="card bg-base-100 border border-base-content/10 shadow-lg p-10 space-y-5">
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
            </section>

            <p class="pt-2 text-center text-sm text-base-content/60">Your information is confidential and protected under medical privacy laws.</p>
        </div>
    </main>

    <script src="{{ asset('js/intake_form.js') }}"></script>
</body>

</html>
