<x-forms.form-container {{ $attributes }}>
    <div class="mb-6">
        <h2 class="card-title font-bold text-2xl">Lifestyle Medicine Assessment</h2>
        <p class="text-base-content/70">Help us understand your current lifestyle and wellness</p>
    </div>

    @php
        $phqQuestions = [
            ['name' => 'phq-little-interest', 'label' => 'Little interest or pleasure in doing things'],
            ['name' => 'phq-feeling-down', 'label' => 'Feeling down, depressed, or hopeless'],
            ['name' => 'phq-trouble-sleeping', 'label' => 'Trouble falling or staying asleep, or sleeping too much'],
            ['name' => 'phq-feeling-tired', 'label' => 'Feeling tired or having little energy'],
            ['name' => 'phq-poor-appetite', 'label' => 'Poor appetite or overeating'],
            ['name' => 'phq-feeling-bad', 'label' => 'Feeling bad about yourself or that you are a failure'],
            ['name' => 'phq-trouble-concentrating', 'label' => 'Trouble concentrating on things'],
            ['name' => 'phq-moving-slow', 'label' => 'Moving or speaking slowly, or being fidgety/restless'],
            ['name' => 'phq-thoughts-hurting', 'label' => 'Thoughts of hurting yourself'],
        ];

        $substances = [
            [
                'id' => 'sub-nicotine',
                'label' => 'Nicotine (cigarettes, vaping)',
                'amountId' => 'sub-nicotine-amount',
                'amountLabel' => 'Amount per day',
                'amountPlaceholder' => 'Enter amount',
                'concernId' => 'sub-nicotine-concern',
            ],
            [
                'id' => 'sub-alcohol',
                'label' => 'Alcohol',
                'amountId' => 'sub-alcohol-amount',
                'amountLabel' => 'Drinks per week',
                'amountPlaceholder' => 'Enter amount',
                'concernId' => 'sub-alcohol-concern',
            ],
            [
                'id' => 'sub-recreational',
                'label' => 'Recreational Drugs',
                'amountId' => 'sub-recreational-amount',
                'amountLabel' => 'Frequency',
                'amountPlaceholder' => 'Enter frequency',
                'concernId' => 'sub-recreational-concern',
            ],
            [
                'id' => 'sub-marijuana',
                'label' => 'Marijuana',
                'amountId' => 'sub-marijuana-amount',
                'amountLabel' => 'Frequency',
                'amountPlaceholder' => 'Enter frequency',
                'concernId' => 'sub-marijuana-concern',
            ],
            [
                'id' => 'sub-screentime',
                'label' => 'Social Media / Screen Time',
                'amountId' => 'sub-screentime-amount',
                'amountLabel' => 'Hours per day',
                'amountPlaceholder' => 'Enter hours',
                'concernId' => 'sub-screentime-concern',
            ],
            [
                'id' => 'sub-gambling',
                'label' => 'Gambling',
                'amountId' => 'sub-gambling-amount',
                'amountLabel' => 'Frequency',
                'amountPlaceholder' => 'Enter frequency',
                'concernId' => 'sub-gambling-concern',
            ],
            [
                'id' => 'sub-others',
                'label' => 'Others',
                'amountId' => 'sub-others-specify',
                'amountLabel' => 'Please specify',
                'amountPlaceholder' => 'Please specify',
                'concernId' => 'sub-others-concern',
            ],
        ];
    @endphp

    <div class="space-y-6">
        <div class="card bg-base-200/50 border border-base-content/10">
            <div class="card-body gap-4">
                <div>
                    <label class="label px-0 pb-2" for="health-score">
                        <span class="label-text text-base font-semibold">Overall Health Score</span>
                    </label>
                    <p class="text-sm text-base-content/70">How would you rate your overall health? (0 = Poor, 10 =
                        Excellent)</p>
                </div>

                <input type="range" id="health-score" min="0" max="10" step="1" value="5"
                    class="range range-sm w-full" />

                <div class="flex items-center justify-between text-sm text-base-content/70">
                    <span>0 - Poor</span>
                    <span id="health-score-display" class="text-lg font-bold text-primary">5</span>
                    <span>10 - Excellent</span>
                </div>
            </div>
        </div>

        <div class="grid gap-6">
            <div class="card bg-base-100 border border-base-content/10 shadow-sm">
                <div class="card-body gap-4">
                    <h3 class="card-title text-lg">Sleep</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="form-control">
                            <label class="label" for="sleep-hours">
                                <span class="label-text">Average hours of sleep per night</span>
                            </label>
                            <input type="number" id="sleep-hours" placeholder="e.g., 7"
                                class="input input-bordered w-full" />
                        </div>
                        <div class="form-control">
                            <label class="label" for="tired-frequency">
                                <span class="label-text">How often do you feel tired?</span>
                            </label>
                            <select id="tired-frequency" class="select select-bordered w-full">
                                <option value="">Select frequency</option>
                                <option value="rarely">Rarely</option>
                                <option value="sometimes">Sometimes</option>
                                <option value="often">Often</option>
                                <option value="always">Always</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 border border-base-content/10 shadow-sm">
                <div class="card-body gap-4">
                    <h3 class="card-title text-lg">Weight Management &amp; Nutrition</h3>
                    <div class="grid gap-4">
                        <div class="form-control">
                            <label class="label" for="weight-perception">
                                <span class="label-text">Current weight perception</span>
                            </label>
                            <select id="weight-perception" class="select select-bordered w-full">
                                <option value="">Select perception</option>
                                <option value="underweight">Underweight</option>
                                <option value="normal">Normal weight</option>
                                <option value="overweight">Overweight</option>
                                <option value="obese">Obese</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label" for="fast-food">
                                <span class="label-text">How often do you eat fast food?</span>
                            </label>
                            <select id="fast-food" class="select select-bordered w-full">
                                <option value="">Select frequency</option>
                                <option value="never">Never</option>
                                <option value="1-2-month">1-2 times per month</option>
                                <option value="1-2-week">1-2 times per week</option>
                                <option value="3-4-week">3-4 times per week</option>
                                <option value="daily">Daily</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-control">
                        <label class="label" for="fruits-veg">
                            <span class="label-text">Daily servings of fruits and vegetables</span>
                        </label>
                        <select id="fruits-veg" class="select select-bordered w-full">
                            <option value="">Select servings</option>
                            <option value="0-1">0-1 servings</option>
                            <option value="2-3">2-3 servings</option>
                            <option value="4-5">4-5 servings</option>
                            <option value="6+">6+ servings</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-content/10 shadow-sm">
            <div class="card-body gap-4">
                <h3 class="card-title text-lg">Exercise</h3>
                <div class="form-control">
                    <label class="label" for="exercise-freq">
                        <span class="label-text">How many days per week do you exercise?</span>
                    </label>
                    <select id="exercise-freq" class="select select-bordered w-full">
                        <option value="">Select frequency</option>
                        <option value="0">0 days</option>
                        <option value="1-2">1-2 days</option>
                        <option value="3-4">3-4 days</option>
                        <option value="5-6">5-6 days</option>
                        <option value="7">7 days</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-content/10 shadow-sm">
            <div class="card-body gap-6">
                <div>
                    <h3 class="card-title text-lg">Mental Health &amp; Well-being</h3>
                    <p class="text-sm text-base-content/70">Over the past 2 weeks, how often have you experienced the
                        following?</p>
                </div>

                <div class="space-y-4 overflow-x-auto">
                    <table class="table">
                        <!-- head -->
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Not at all</th>
                                <th>Several days</th>
                                <th>More than half</th>
                                <th>Nearly every day</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($phqQuestions as $question)
                                <tr>
                                    <th>
                                        <p class="font-medium">{{ $question['label'] }}</p>
                                    </th>
                                    <th class="text-center">
                                        <input type="radio" name="{{ $question['name'] }}" value="not-at-all"
                                            class="radio radio-sm" />
                                    </th>
                                    <th class="text-center">
                                        <input type="radio" name="{{ $question['name'] }}" value="several-days"
                                            class="radio radio-sm" />
                                    </th>
                                    <th class="text-center">
                                        <input type="radio" name="{{ $question['name'] }}" value="more-than-half"
                                            class="radio radio-sm" />
                                    </th>
                                    <th class="text-center">
                                        <input type="radio" name="{{ $question['name'] }}" value="nearly-every-day"
                                            class="radio radio-sm" />
                                    </th>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-content/10 shadow-sm">
            <div class="card-body gap-6">
                <div>
                    <h3 class="card-title text-lg">Substance Use and Addictive Behaviors</h3>
                    <p class="text-sm text-base-content/70">Please indicate if you use any of the following, and your
                        level of concern</p>
                </div>

                <div class="space-y-4">
                    @foreach ($substances as $substance)
                        <div class="rounded-box border border-base-content/10 bg-base-200/40 p-4">
                            <label class="label cursor-pointer justify-start gap-3 px-0 pt-0">
                                <input type="checkbox" id="{{ $substance['id'] }}"
                                    data-expands="{{ $substance['id'] }}-expand" class="checkbox checkbox-sm" />
                                <span class="label-text text-base font-medium">{{ $substance['label'] }}</span>
                            </label>

                            <div class="expand-target hidden mt-4 space-y-4 pl-8" id="{{ $substance['id'] }}-expand">
                                <div class="form-control">
                                    <label class="label" for="{{ $substance['amountId'] }}">
                                        <span class="label-text">{{ $substance['amountLabel'] }}</span>
                                    </label>
                                    <input type="text" id="{{ $substance['amountId'] }}"
                                        placeholder="{{ $substance['amountPlaceholder'] }}"
                                        class="input input-bordered w-full" />
                                </div>

                                <div class="form-control">
                                    <label class="label" for="{{ $substance['concernId'] }}">
                                        <span class="label-text">Level of concern (0 = No concern, 5 = Very
                                            concerned)</span>
                                    </label>
                                    <input type="range" class="range range-sm w-full concern-slider"
                                        id="{{ $substance['concernId'] }}" min="0" max="5"
                                        step="1" value="0" />
                                    <div class="mt-2 flex items-center justify-between text-sm text-base-content/70">
                                        <span>0</span>
                                        <span class="slider-value concern-display text-base-content font-bold"
                                            data-for="{{ $substance['concernId'] }}">0</span>
                                        <span>5</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card border border-base-content/10 shadow-sm bg-green-50">
            <div class="card-body gap-4">
                <h3 class="card-title text-lg">Motivation for Change</h3>
                <div class="form-control">
                    <label class="label" for="lifestyle-motivation">
                        <span class="label-text">What are your top 3 lifestyle areas you're motivated to change?</span>
                    </label>
                    <p class="text-sm text-base-content/70">Rank from 1 (most important) to 3</p>
                    <textarea id="lifestyle-motivation" rows="3"
                        placeholder="1. Sleep better&#10;2. Exercise more&#10;3. Reduce stress" class="textarea textarea-bordered w-full"></textarea>
                </div>
                <div class="form-control">
                    <label class="label" for="motivation-level">
                        <span class="label-text">How motivated are you to be healthier?</span>
                    </label>
                    <select id="motivation-level" class="select select-bordered w-full">
                        <option value="">Select motivation level</option>
                        <option value="not-motivated">Not motivated</option>
                        <option value="somewhat">Somewhat motivated</option>
                        <option value="motivated">Motivated</option>
                        <option value="very-motivated">Very motivated</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</x-forms.form-container>
