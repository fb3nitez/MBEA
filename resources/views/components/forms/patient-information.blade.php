<x-forms.form-container {{ $attributes }}>
    <div class="mb-6">
        <h2 class="card-title text-2xl">Patient Information</h2>
        <p class="text-base-content/70">
            Please provide your basic information
        </p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        <!-- Full Name -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Full Name *</span>
            </label>
            <input type="text" id="name" placeholder="Enter your full name"
                class="input input-bordered w-full" />
        </div>

        <!-- Age -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Age *</span>
            </label>
            <input type="number" id="age" placeholder="Your age" class="input input-bordered w-full" />
        </div>

        <!-- Birthday -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Birthday *</span>
            </label>
            <input type="date" id="birthday" class="input input-bordered w-full" />
        </div>

        <!-- Religion -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Religion</span>
            </label>
            <input type="text" id="religion" placeholder="Your religion" class="input input-bordered w-full" />
        </div>

        <!-- Sex -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Sex *</span>
            </label>
            <select id="sex" class="select select-bordered w-full">
                <option value="">Select sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="intersex">Intersex</option>
            </select>
        </div>

        <!-- Gender -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Gender</span>
            </label>
            <input type="text" id="gender" placeholder="Your gender identity"
                class="input input-bordered w-full" />
        </div>

        <!-- Marital Status -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Marital Status</span>
            </label>
            <select id="marital-status" class="select select-bordered w-full">
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
            <input type="text" id="year-level" placeholder="e.g., 3rd Year"
                class="input input-bordered w-full" />
        </div>

        <!-- Course -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text">Course</span>
            </label>
            <input type="text" id="course" placeholder="Your course or program"
                class="input input-bordered w-full" />
        </div>

        <!-- Occupation -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text">Occupation</span>
            </label>
            <input type="text" id="occupation" placeholder="Your occupation"
                class="input input-bordered w-full" />
        </div>

        <!-- Chief Complaint -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text">
                    Chief Complaint / Reason for Consultation *
                </span>
            </label>
            <textarea id="chief-complaint" rows="4" placeholder="What brings you here today?"
                class="textarea textarea-bordered w-full"></textarea>
        </div>

        <!-- Primary Diagnosis -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text">Primary Diagnosis (if known)</span>
            </label>
            <input type="text" id="primary-diagnosis" placeholder="Enter diagnosis if applicable"
                class="input input-bordered w-full" />
        </div>

    </div>
</x-forms.form-container>
