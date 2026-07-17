<div class="mb-6">
    <h2 class="card-title font-bold text-2xl">Medical History</h2>
    <p class="text-base-content/70">Please indicate any relevant medical conditions</p>
</div>

<!-- A. Personal Medical History -->
<div class="mb-8">
    <h3 class="mb-1 text-lg font-semibold">A. Personal Medical History</h3>
    <p class="mb-4 text-sm text-base-content/70">Check all that apply:</p>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="pmhHypertension" class="checkbox checkbox-sm" />
            <span class="label-text">Hypertension</span>
        </label>

        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="pmhStroke" class="checkbox checkbox-sm" />
            <span class="label-text">Stroke or TIA</span>
        </label>

        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="pmhTuberculosis" class="checkbox checkbox-sm" />
            <span class="label-text">Tuberculosis</span>
        </label>

        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="pmhThyroid" class="checkbox checkbox-sm" />
            <span class="label-text">Thyroid Disorders</span>
        </label>

        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="pmhDiabetes" class="checkbox checkbox-sm" />
            <span class="label-text">Diabetes Mellitus</span>
        </label>

        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="pmhChronicPain" class="checkbox checkbox-sm" />
            <span class="label-text">Chronic Pain / Fibromyalgia</span>
        </label>

        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="pmhAsthma" class="checkbox checkbox-sm" />
            <span class="label-text">Bronchial Asthma</span>
        </label>

        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="pmhEpilepsy" class="checkbox checkbox-sm" />
            <span class="label-text">Epilepsy / Seizure Disorder</span>
        </label>
    </div>

    <!-- Autoimmune -->
    <div class="mt-4 border border-base-content/20 p-3 rounded">
        <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox" name="pmhAutoimmune" class="checkbox checkbox-sm" data-expands="autoimmune-expand" />
            <span class="label-text">Autoimmune Disease</span>
        </label>
        <div id="autoimmune-expand" class="hidden mt-2">
            <input type="text" name="pmhAutoimmuneSpecify" placeholder="Please specify" class="input input-bordered w-full" />
        </div>
    </div>

    <!-- Cancer -->
    <div class="mt-4 border border-base-content/20 p-3 rounded">
        <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox" name="pmhCancer" class="checkbox checkbox-sm" data-expands="cancer-expand" />
            <span class="label-text">Cancer</span>
        </label>
        <div id="cancer-expand" class="hidden mt-2">
            <input type="text" name="pmhCancerSpecify" placeholder="Please specify type" class="input input-bordered w-full" />
        </div>
    </div>

    <!-- Other -->
    <div class="mt-4 border border-base-content/20 p-3 rounded">
        <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox" name="pmhOther" class="checkbox checkbox-sm" data-expands="pmh-other-expand" />
            <span class="label-text">Other</span>
        </label>
        <div id="pmh-other-expand" class="hidden mt-2">
            <input type="text" name="pmhOtherSpecify" placeholder="Please specify" class="input input-bordered w-full" />
        </div>
    </div>

    <!-- Medications -->
    <div class="form-control mt-6">
        <label class="label">
            <span class="label-text">Medications Currently Taking</span>
        </label>
        <textarea name="currentMedications" rows="4" placeholder="List all current medications, including dosage" class="textarea textarea-bordered w-full"></textarea>
    </div>
</div>

<div class="divider"></div>

<!-- B. Family Medical and Psychiatric History -->
<div>
    <h3 class="mb-1 text-lg font-semibold">B. Family Medical and Psychiatric History</h3>
    <p class="mb-4 text-sm text-base-content/70">Check all that apply to family members:</p>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="fhHypertension" class="checkbox checkbox-sm" />
            <span class="label-text">Hypertension</span>
        </label>

        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="fhStroke" class="checkbox checkbox-sm" />
            <span class="label-text">Stroke</span>
        </label>

        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="fhDiabetes" class="checkbox checkbox-sm" />
            <span class="label-text">Diabetes Mellitus</span>
        </label>

        <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
            <input type="checkbox" name="fhSubstance" class="checkbox checkbox-sm" />
            <span class="label-text">Substance Use Disorder</span>
        </label>
    </div>

    <!-- Cancer -->
    <div class="mt-4 border border-base-content/20 p-3 rounded">
        <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox" name="fhCancer" class="checkbox checkbox-sm" data-expands="fh-cancer-expand" />
            <span class="label-text">Cancer</span>
        </label>
        <div id="fh-cancer-expand" class="hidden mt-2 grid gap-3">
            <input type="text" name="fhCancerType" placeholder="Specify type" class="input input-bordered w-full" />
            <input type="text" name="fhCancerRelation" placeholder="Relation (e.g., mother, father)" class="input input-bordered w-full" />
        </div>
    </div>

    <!-- Psychiatric Disorders -->
    <div class="mt-4 border border-base-content/20 p-3 rounded">
        <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox" name="fhPsychiatric" class="checkbox checkbox-sm" data-expands="fh-psychiatric-expand" />
            <span class="label-text">Psychiatric Disorders (Depression / Bipolar / Schizophrenia)</span>
        </label>
        <div id="fh-psychiatric-expand" class="hidden mt-2 grid gap-3">
            <input type="text" name="fhPsychiatricType" placeholder="Specify disorder" class="input input-bordered w-full" />
            <input type="text" name="fhPsychiatricRelation" placeholder="Relation (e.g., sibling, parent)" class="input input-bordered w-full" />
        </div>
    </div>

    <!-- Other -->
    <div class="mt-4 border border-base-content/20 p-3 rounded">
        <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox" name="fhOther" class="checkbox checkbox-sm" data-expands="fh-other-expand" />
            <span class="label-text">Other</span>
        </label>
        <div id="fh-other-expand" class="hidden mt-2 grid gap-3">
            <input type="text" name="fhOtherSpecify" placeholder="Specify condition" class="input input-bordered w-full" />
            <input type="text" name="fhOtherRelation" placeholder="Relation" class="input input-bordered w-full" />
        </div>
    </div>
</div>
