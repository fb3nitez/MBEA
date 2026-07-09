<section id="patient-information-form" {{ $attributes->merge(['class' => 'card bg-base-100 border border-base-content/10 shadow-lg']) }}>
    <div class="card-body">
        {{ $slot }}

        <div class="flex border-t border-base-content/10 mt-10 pt-5">
            <button class="ms-auto btn btn-primary">Next <i class="fa-solid fa-arrow-right"></i></button>
        </div>
    </div>
</section>
