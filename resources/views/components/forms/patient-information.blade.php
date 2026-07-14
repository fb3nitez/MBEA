<x-forms.form-container {{ $attributes }}>
    <div class="mb-6">
        <h2 class="card-title font-bold text-2xl">Patient Information</h2>
        <p class="text-base-content/70">Please provide your basic information</p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <!-- Full Name -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Full Name <span class="text-error">*</span></span>
            </label>
            <input type="text" wire:model="name" placeholder="Enter your full name" class="input input-bordered w-full" />
            @error('name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Birthday -->
        <div class="form-control">
            <label class="label flex">
                <span class="label-text">Birthday <span class="text-error">*</span></span>
                <span id="age" class="label-text ml-auto text-success"></span>
            </label>
            <input type="date" wire:model="birthday" id="birthday" class="input input-bordered w-full" />
            @error('birthday') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Religion -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Religion</span>
            </label>
            <input type="text" wire:model="religion" placeholder="Your religion" class="input input-bordered w-full" />
        </div>

        <!-- Sex -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Sex <span class="text-error">*</span></span>
            </label>
            <select wire:model="sex" class="select select-bordered w-full">
                <option value="">Select sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="intersex">Intersex</option>
            </select>
            @error('sex') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Gender -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Gender</span>
            </label>
            <select wire:model="gender" id="genderSelector" class="select select-bordered w-full">
                <option value="Straight">Straight</option>
                <option value="Lesbian">Lesbian</option>
                <option value="Gay">Gay</option>
                <option value="Bisexual">Bisexual</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" wire:model="gender" id="genderInput" placeholder="Enter Gender" class="input input-bordered w-full mt-2 hidden" />
        </div>

        <!-- Marital Status -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Marital Status</span>
            </label>
            <select wire:model="maritalStatus" class="select select-bordered w-full">
                <option value="">Select status</option>
                <option value="single">Single</option>
                <option value="married">Married</option>
                <option value="divorced">Divorced</option>
                <option value="widowed">Widowed</option>
                <option value="separated">Separated</option>
            </select>
        </div>

        <!-- Student Year Level -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Student Year Level</span>
            </label>
            <input type="text" wire:model="yearLevel" placeholder="e.g., 3rd Year" class="input input-bordered w-full" />
        </div>

        <!-- Course -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Course</span>
            </label>
            <input type="text" wire:model="course" placeholder="Your course or program" class="input input-bordered w-full" />
        </div>

        <!-- Occupation -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text">Occupation</span>
            </label>
            <input type="text" wire:model="occupation" placeholder="Your occupation" class="input input-bordered w-full" />
        </div>

        <!-- Chief Complaint -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text">Chief Complaint / Reason for Consultation <span class="text-error">*</span></span>
            </label>
            <textarea wire:model="chiefComplaint" rows="4" placeholder="What brings you here today?" class="textarea textarea-bordered w-full"></textarea>
            @error('chiefComplaint') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Primary Diagnosis -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text">Primary Diagnosis (if known)</span>
            </label>
            <input type="text" wire:model="primaryDiagnosis" placeholder="Enter diagnosis if applicable" class="input input-bordered w-full" />
        </div>
    </div>
</x-forms.form-container>
