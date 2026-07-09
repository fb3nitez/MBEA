<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite(['resources/css/app.css'])
    <script src="https://kit.fontawesome.com/d818f87454.js" crossorigin="anonymous"></script>
</head>
<body class="bg-base-200">
    <main class="mx-auto px-5 pt-2 pb-10 min-w-3xl w-min">
        <section id="page-title-container" class="mt-5">
            <h1 class="text-center font-bold text-3xl">Patient Intake Form</h1>
            <p class="text-center text-base-content/80 mt-2">Please complete all sections before your consultation</p>
        </section>
        <section id="progress-tracker-container" class="mt-10">
            <div class="flex justify-between text-sm text-base-content/80">
                <p>
                    Step <span id="current-form-step">2</span> of <span id="max-form-step">5</span>
                </p>
                <p>
                    <span id="step-percentage">20%</span> Complete
                </p>
            </div>
            <progress class="progress w-full" value="20" max="100"></progress>
        </section>

        <x-forms.patient-information class="mt-5" />
        <x-forms.medical-history class="mt-5" />
    </main>
</body>
</html>
