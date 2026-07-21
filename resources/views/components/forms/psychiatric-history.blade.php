<div class="mb-6">
    <h2 class="card-title font-bold text-2xl">Personal History</h2>
    <p class="text-base-content/70">Your responses are confidential and help us provide better care</p>
</div>

<div class="space-y-8">
    <div class="space-y-4">
        <div>
            <h3 class="text-lg font-semibold">A. Mental Health Diagnosis</h3>
        </div>

        <div class="space-y-3">
            <p class="text-sm text-base-content/70">Have you been diagnosed with a mental health condition?</p>
            <div class="grid gap-3 md:grid-cols-2">
                <label class="label cursor-pointer justify-start gap-3 rounded-box border border-base-content/10 px-4 py-3 hover:bg-base-200/60">
                    <input type="radio" name="diagnosedMH" value="yes" class="radio radio-sm" />
                    <span class="label-text">Yes</span>
                </label>
                <label class="label cursor-pointer justify-start gap-3 rounded-box border border-base-content/10 px-4 py-3 hover:bg-base-200/60">
                    <input type="radio" name="diagnosedMH" value="no" class="radio radio-sm" />
                    <span class="label-text">No</span>
                </label>
            </div>

            <div id="diagnosed-expand" class="hidden rounded-box border border-base-content/10 bg-base-200/50 p-4">
                <div class="form-control">
                    <label class="label" for="diagnosis-specify">
                        <span class="label-text">Please specify the diagnosis</span>
                    </label>
                    <input type="text" name="diagnosisSpecify" id="diagnosis-specify" placeholder="e.g., Depression, Anxiety, etc." class="input input-bordered w-full" />
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <p class="text-sm text-base-content/70">Have you been hospitalized for psychiatric reasons?</p>
            <div class="grid gap-3 md:grid-cols-2">
                <label class="label cursor-pointer justify-start gap-3 rounded-box border border-base-content/10 px-4 py-3 hover:bg-base-200/60">
                    <input type="radio" name="hospitalized" value="yes" class="radio radio-sm" />
                    <span class="label-text">Yes</span>
                </label>
                <label class="label cursor-pointer justify-start gap-3 rounded-box border border-base-content/10 px-4 py-3 hover:bg-base-200/60">
                    <input type="radio" name="hospitalized" value="no" class="radio radio-sm" />
                    <span class="label-text">No</span>
                </label>
            </div>

            <div id="hospitalized-expand" class="hidden rounded-box border border-base-content/10 bg-base-200/50 p-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="form-control">
                        <label class="label" for="hosp-times">
                            <span class="label-text">Number of times</span>
                        </label>
                        <input type="number" name="hospTimes" id="hosp-times" placeholder="Enter number" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label" for="hosp-when">
                            <span class="label-text">When (approximate date/year)</span>
                        </label>
                        <input type="text" name="hospWhen" id="hosp-when" placeholder="e.g., 2020, January 2023" class="input input-bordered w-full" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="space-y-4">
        <div>
            <h3 class="text-lg font-semibold">B. History of Trauma and Abuse</h3>
            <p class="text-sm text-base-content/70">The following questions help us understand your background. All information is confidential.</p>
        </div>

        @php
        $traumaGroups = [
            ['id' => 'physical', 'label' => 'Physical Abuse', 'checkId' => 'traumaPhysical', 'expandId' => 'trauma-physical-expand', 'detailsId' => 'tpDetails', 'prefix' => 'tp'],
            ['id' => 'emotional', 'label' => 'Emotional Abuse', 'checkId' => 'traumaEmotional', 'expandId' => 'trauma-emotional-expand', 'detailsId' => 'teDetails', 'prefix' => 'te'],
            ['id' => 'sexual', 'label' => 'Sexual Abuse', 'checkId' => 'traumaSexual', 'expandId' => 'trauma-sexual-expand', 'detailsId' => 'tsDetails', 'prefix' => 'ts'],
            ['id' => 'neglect', 'label' => 'Neglect', 'checkId' => 'traumaNeglect', 'expandId' => 'trauma-neglect-expand', 'detailsId' => 'tnDetails', 'prefix' => 'tn'],
        ];
        @endphp

        <div class="space-y-4">
            @foreach ($traumaGroups as $group)
            <div class="rounded-box border border-base-content/10 bg-base-200/40 p-4">
                <label class="label cursor-pointer justify-start gap-3 px-0 pt-0">
                    <input type="checkbox" name="{{ $group['checkId'] }}" class="checkbox checkbox-sm" data-expands="{{ $group['expandId'] }}" />
                    <span class="label-text text-base font-medium">{{ $group['label'] }}</span>
                </label>
                <div class="expand-target hidden mt-4 space-y-4 pl-8" id="{{ $group['expandId'] }}">
                    <p class="text-sm font-medium text-base-content/70">When did this occur?</p>
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="label cursor-pointer justify-start gap-3 rounded-box border border-base-content/10 px-4 py-3 hover:bg-base-100">
                            <input type="checkbox" name="{{ $group['prefix'] }}Child" class="checkbox checkbox-sm" />
                            <span class="label-text">As a child</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-3 rounded-box border border-base-content/10 px-4 py-3 hover:bg-base-100">
                            <input type="checkbox" name="{{ $group['prefix'] }}Adult" class="checkbox checkbox-sm" />
                            <span class="label-text">As an adult</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-3 rounded-box border border-base-content/10 px-4 py-3 hover:bg-base-100">
                            <input type="checkbox" name="{{ $group['prefix'] }}Ongoing" class="checkbox checkbox-sm" />
                            <span class="label-text">Ongoing</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-3 rounded-box border border-base-content/10 px-4 py-3 hover:bg-base-100">
                            <input type="checkbox" name="{{ $group['prefix'] }}Past" class="checkbox checkbox-sm" />
                            <span class="label-text">Past experience</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label" for="{{ $group['detailsId'] }}">
                            <span class="label-text">Additional details (optional)</span>
                        </label>
                        <textarea name="{{ $group['detailsId'] }}" id="{{ $group['detailsId'] }}" rows="2" placeholder="You may provide additional context if comfortable" class="textarea textarea-bordered w-full"></textarea>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
