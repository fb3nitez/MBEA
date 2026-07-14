<x-forms.form-container {{ $attributes }}>
    <div class="mb-6">
        <h2 class="card-title font-bold text-2xl">Medical History</h2>
        <p class="text-base-content/70">
            Please indicate any relevant medical conditions
        </p>
    </div>

    <!-- A. Personal Medical History -->
    <div class="mb-8">
        <h3 class="mb-1 text-lg font-semibold">A. Personal Medical History</h3>
        <p class="mb-4 text-sm text-base-content/70">
            Check all that apply:
        </p>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="pmh-hypertension" class="checkbox checkbox-sm" />
                <span class="label-text">Hypertension</span>
            </label>

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="pmh-stroke" class="checkbox checkbox-sm" />
                <span class="label-text">Stroke or TIA</span>
            </label>

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="pmh-tuberculosis" class="checkbox checkbox-sm" />
                <span class="label-text">Tuberculosis</span>
            </label>

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="pmh-thyroid" class="checkbox checkbox-sm" />
                <span class="label-text">Thyroid Disorders</span>
            </label>

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="pmh-diabetes" class="checkbox checkbox-sm" />
                <span class="label-text">Diabetes Mellitus</span>
            </label>

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="pmh-chronic-pain" class="checkbox checkbox-sm" />
                <span class="label-text">Chronic Pain / Fibromyalgia</span>
            </label>

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="pmh-asthma" class="checkbox checkbox-sm" />
                <span class="label-text">Bronchial Asthma</span>
            </label>

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="pmh-epilepsy" class="checkbox checkbox-sm" />
                <span class="label-text">Epilepsy / Seizure Disorder</span>
            </label>

        </div>

        <!-- Autoimmune -->
        <div class="mt-4 border border-base-content/20 p-3 rounded">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" id="pmh-autoimmune" class="checkbox checkbox-sm" data-expands="autoimmune-expand" />
                <span class="label-text">Autoimmune Disease</span>
            </label>

            <div id="autoimmune-expand" class="hidden mt-2">
                <input type="text"
                    id="pmh-autoimmune-specify"
                    placeholder="Please specify"
                    class="input input-bordered w-full" />
            </div>
        </div>

        <!-- Cancer -->
        <div class="mt-4 border border-base-content/20 p-3 rounded">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" id="pmh-cancer" class="checkbox checkbox-sm" data-expands="cancer-expand" />
                <span class="label-text">Cancer</span>
            </label>

            <div id="cancer-expand" class="hidden mt-2">
                <input type="text"
                    id="pmh-cancer-specify"
                    placeholder="Please specify type"
                    class="input input-bordered w-full" />
            </div>
        </div>

        <!-- Other -->
        <div class="mt-4 border border-base-content/20 p-3 rounded">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" id="pmh-other" class="checkbox checkbox-sm" data-expands="pmh-other-expand" />
                <span class="label-text">Other</span>
            </label>

            <div id="pmh-other-expand" class="hidden mt-2">
                <input type="text"
                    id="pmh-other-specify"
                    placeholder="Please specify"
                    class="input input-bordered w-full" />
            </div>
        </div>

        <!-- Medications -->
        <div class="form-control mt-6">
            <label class="label">
                <span class="label-text">Medications Currently Taking</span>
            </label>

            <textarea
                id="current-medications"
                rows="4"
                placeholder="List all current medications, including dosage"
                class="textarea textarea-bordered w-full"></textarea>
        </div>
    </div>

    <div class="divider"></div>

    <!-- B. Family Medical and Psychiatric History -->
    <div>
        <h3 class="mb-1 text-lg font-semibold">
            B. Family Medical and Psychiatric History
        </h3>

        <p class="mb-4 text-sm text-base-content/70">
            Check all that apply to family members:
        </p>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="fh-hypertension" class="checkbox checkbox-sm" />
                <span class="label-text">Hypertension</span>
            </label>

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="fh-stroke" class="checkbox checkbox-sm" />
                <span class="label-text">Stroke</span>
            </label>

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="fh-diabetes" class="checkbox checkbox-sm" />
                <span class="label-text">Diabetes Mellitus</span>
            </label>

            <label class="label cursor-pointer justify-start gap-3 border border-base-content/20 p-3 rounded">
                <input type="checkbox" id="fh-substance" class="checkbox checkbox-sm" />
                <span class="label-text">Substance Use Disorder</span>
            </label>

        </div>

        <!-- Cancer -->
        <div class="mt-4 border border-base-content/20 p-3 rounded">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" id="fh-cancer" class="checkbox checkbox-sm" data-expands="fh-cancer-expand" />
                <span class="label-text">Cancer</span>
            </label>

            <div id="fh-cancer-expand" class="hidden mt-2 grid gap-3">
                <input type="text"
                    id="fh-cancer-type"
                    placeholder="Specify type"
                    class="input input-bordered w-full" />

                <input type="text"
                    id="fh-cancer-relation"
                    placeholder="Relation (e.g., mother, father)"
                    class="input input-bordered w-full" />
            </div>
        </div>

        <!-- Psychiatric Disorders -->
        <div class="mt-4 border border-base-content/20 p-3 rounded">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" id="fh-psychiatric" class="checkbox checkbox-sm" data-expands="fh-psychiatric-expand" />
                <span class="label-text">
                    Psychiatric Disorders (Depression / Bipolar / Schizophrenia)
                </span>
            </label>

            <div id="fh-psychiatric-expand" class="hidden mt-2 grid gap-3">
                <input type="text"
                    id="fh-psychiatric-type"
                    placeholder="Specify disorder"
                    class="input input-bordered w-full" />

                <input type="text"
                    id="fh-psychiatric-relation"
                    placeholder="Relation (e.g., sibling, parent)"
                    class="input input-bordered w-full" />
            </div>
        </div>

        <!-- Other -->
        <div class="mt-4 border border-base-content/20 p-3 rounded">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" id="fh-other" class="checkbox checkbox-sm" data-expands="fh-other-expand" />
                <span class="label-text">Other</span>
            </label>

            <div id="fh-other-expand" class="hidden mt-2 grid gap-3">
                <input type="text"
                    id="fh-other-specify"
                    placeholder="Specify condition"
                    class="input input-bordered w-full" />

                <input type="text"
                    id="fh-other-relation"
                    placeholder="Relation"
                    class="input input-bordered w-full" />
            </div>
        </div>
    </div>
</x-forms.form-container>
