@php
  $form = $prescription->form_data ?? [];
  $hasChoice = static fn (string $key, string $choice): bool => in_array($choice, (array) ($form[$key] ?? []), true);
  $cell = static fn (string $key, int $row, int $column): mixed => data_get($form, "{$key}.{$row}.{$column}");
  $exerciseGoals = [
    'Feel happier and less anxious',
    'Sleep better',
    'More energy',
    'Lose weight or prevent weight gain',
    'Lower blood pressure, blood sugar or cholesterol',
    'Stronger muscles and bones',
    'Better focus and thinking',
    'Other',
  ];
  $exerciseSafetyQuestions = [
    'Chest pain, pressure or tightness with activity',
    'Fainting or severe dizziness',
    'Heart disease, stroke, or a heart condition',
    'Uncontrolled high blood pressure or diabetes',
    'Bone, joint or back problems that limit movement',
    'Pregnancy or recent surgery',
    'Any other reason your doctor said to avoid exercise',
  ];
  $exerciseSpecialInstructions = [
    'Lithium: Drink plenty of fluids, avoid heavy sweating in the heat, and do not exercise to the point of dehydration.',
    'Antipsychotics or blood pressure medicines: Stand up slowly. Dizziness after exercise can occur.',
    'Medicines that cause drowsiness: Exercise when you feel most alert and be careful with swimming and cycling.',
    'Diabetes medicines: Check your blood sugar and carry a snack in case of low sugar.',
    'Weight gain from medicines: Combine exercise with the Nutrition Prescription.',
    'Other',
  ];
  $exerciseTips = [
    'Just put on your shoes and walk for 5 minutes. You may continue if you feel like it.',
    'Exercise with a friend, family member or group.',
    'Pick something you enjoy: music, dancing, a park, a pet.',
    'Set a reminder on your phone at the same time each day.',
    'Plan for obstacles (rain, tiredness) and have a Plan B (indoor walking, stretching at home).',
    'Reward yourself with something other than food.',
  ];
  $nutritionGoals = [
    'Improve mood and energy',
    'Sleep better',
    'Lose weight / prevent weight gain from medicines',
    'Lower blood sugar',
    'Lower blood pressure or cholesterol',
    'Eat more regularly (stop skipping meals)',
    'Cut down on sugar, alcohol or caffeine',
    'Other',
  ];
  $nutritionTargets = [
    ['Vegetables', '3 or more servings', '1 fist-size or 1 cup raw leafy greens'],
    ['Fruit', '2 servings', '1 medium fruit'],
    ['Whole grains', '3 servings', '½ cup cooked rice or oats, 1 slice bread'],
    ['Protein', '2 to 3 palm-size portions', '1 palm of fish, chicken, tofu or 1 cup beans'],
    ['Fish (especially oily fish)', '2 times per week', 'Sardines, mackerel, salmon, tuna'],
    ['Nuts and seeds', '1 small handful', 'About 30 g'],
    ['Water', '6 to 8 glasses', '1 glass = 250 mL'],
  ];
  $nutritionMoreFoods = [
    'Colorful vegetables and fruits',
    'Beans, lentils, chickpeas',
    'Oily fish, eggs',
    'Whole grains and oats',
    'Nuts, seeds, olive oil',
    'Yogurt, fermented foods (kimchi, tempeh, kefir)',
    'Leafy greens',
  ];
  $nutritionLessFoods = [
    'Sugary drinks (soda, sweetened coffee and tea, juice)',
    'Sweets, pastries, cookies',
    'Fast food and deep-fried foods',
    'Processed meats (hotdogs, bacon, luncheon meat)',
    'Instant noodles and packaged snacks',
  ];
  $nutritionSpecialInstructions = [
    'Weight-gaining medicines: Choose high-fiber, high-protein foods. Avoid sugary drinks and late-night eating.',
    'Lithium: Keep salt and fluid intake steady. Drink enough water. Do not suddenly change your salt intake or go on crash diets.',
    'MAOI medicines: Avoid aged cheese, cured meats, soy sauce, fermented foods and tap beer. Ask your doctor for the full list.',
    'Warfarin: Keep leafy green intake consistent from week to week.',
    'Grapefruit: Avoid, unless my doctor or pharmacist says it is safe.',
    'Other',
  ];
  $followUpItems = $prescription->type === 'exercise'
    ? ['New pain or injury', 'Unusual tiredness or breathlessness', 'Trouble keeping up with the plan', 'Worsening mood or thoughts of self-harm']
    : ['Severe loss of appetite', 'Vomiting or diarrhea that does not stop', 'Rapid weight change', 'Worsening mood or thoughts of self-harm'];
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <style>
    @page { size: letter; margin: 1in; }
    * { box-sizing: border-box; }
    body { color: #111; font: 9pt/1.25 "DejaVu Sans", sans-serif; }
    h1 { font-size: 17pt; margin: 0 0 3pt; }
    h2 { font-size: 11pt; margin: 11pt 0 4pt; page-break-after: avoid; }
    p { margin: 3pt 0; }
    table { border-collapse: collapse; margin: 4pt 0; width: 100%; }
    th, td { border: 1px solid #444; padding: 4pt; text-align: left; vertical-align: top; }
    th { font-weight: bold; }
    .clinic-date { border-bottom: 1px solid #444; margin-bottom: 8pt; padding-bottom: 5pt; }
    .box { border: 1px solid #111; display: inline-block; font-size: 7pt; height: 9pt; line-height: 8pt; margin-right: 3pt; text-align: center; vertical-align: -1pt; width: 9pt; }
    .choice { display: inline-block; margin: 1pt 7pt 1pt 0; }
    .choice-block { display: block; margin: 2pt 0; }
    .field-line { border-bottom: 1px solid #555; display: inline-block; min-width: 90pt; }
    .small { font-size: 8pt; }
    .center { text-align: center; }
    .signature { border-bottom: 1px solid #111; display: inline-block; margin-top: 12pt; min-width: 180pt; }
    .footer { border-top: 1px solid #777; font-size: 8pt; margin-top: 12pt; padding-top: 5pt; }
    tr { page-break-inside: avoid; }
  </style>
</head>
<body>
  <h1>{{ ucfirst($prescription->type) }} Prescription</h1>
  <p class="clinic-date">Lifestyle Medicine Clinic | Prescribed by: {{ $prescriberName }} | Date: {{ $prescription->prescription_date->format('m / d / Y') }}</p>

  @if ($prescription->type === 'exercise')
    <h2>1. About You</h2>
    <table>
      <tr><th>Name</th><td colspan="3">{{ $prescription->patient_name }}</td></tr>
      <tr><th>Age / Sex</th><td colspan="3">{{ $prescription->patient_age }} / {{ $prescription->patient_sex }}</td></tr>
      <tr><th>Height / Weight / BMI</th><td colspan="3">{{ $form['hwb'] ?? '' }}</td></tr>
      <tr><th>Resting heart rate / Blood pressure</th><td colspan="3">{{ $form['vitals'] ?? '' }}</td></tr>
      <tr>
        <th>Current activity level</th>
        <td colspan="3">
          @foreach (['Mostly sitting', 'Light (walks sometimes)', 'Active (exercise 1 to 2 days/week)', 'Very active'] as $option)
            <span class="choice"><span class="box">{{ ($form['activity'] ?? '') === $option ? 'X' : '' }}</span>{{ $option }}</span>
          @endforeach
        </td>
      </tr>
      <tr><th>Current medicines</th><td colspan="3">{{ $form['meds'] ?? '' }}</td></tr>
    </table>

    <h2>2. Safety Check (to be completed with your doctor)</h2>
    <p>Have you ever had or do you now have:</p>
    <table>
      <tr><th>Question</th><th class="center">Yes</th><th class="center">No</th></tr>
      @foreach ($exerciseSafetyQuestions as $index => $question)
        @php($answer = $form['safety'][$index] ?? null)
        <tr>
          <td>{{ $question }}</td>
          <td class="center"><span class="box">{{ $answer === 'Yes' ? 'X' : '' }}</span></td>
          <td class="center"><span class="box">{{ $answer === 'No' ? 'X' : '' }}</span></td>
        </tr>
      @endforeach
    </table>
    <p>If you answered YES to any item: your doctor must clear you before you start.</p>
    <p><span class="box">{{ $hasChoice('cleared', 'Cleared') ? 'X' : '' }}</span> Cleared on <span class="field-line">{{ $form['cleardate'] ?? '' }}</span></p>

    <h2>3. My Goals (check up to 3)</h2>
    @foreach ($exerciseGoals as $goal)
      <p class="choice-block"><span class="box">{{ $hasChoice('goals', $goal) ? 'X' : '' }}</span>{{ $goal === 'Other' ? 'Other: ' . ($form['goalother'] ?? '______________________________') : $goal }}</p>
    @endforeach

    <h2>4. My Weekly Exercise Plan</h2>
    <table>
      <tr><th>Type</th><th>What it is</th><th>How much</th><th>My choice</th></tr>
      @foreach ([
        ['Aerobic (cardio)', 'Brisk walking, cycling, swimming, dancing, jogging', 'Goal: 150 minutes per week (for example 30 minutes, 5 days)'],
        ['Strength', 'Body weight, resistance bands, weights, push-ups, squats', '2 days per week, 8 to 10 exercises, 1 to 2 sets of 8 to 12 repeats'],
        ['Stretching and balance', 'Stretching, yoga, tai chi', '2 to 3 days per week, 5 to 10 minutes'],
        ['Move more all day', 'Stand up, take stairs, short walks', 'Get up and move for 2 to 3 minutes every hour of sitting'],
      ] as $index => $row)
        <tr><td>{{ $row[0] }}</td><td>{{ $row[1] }}</td><td>{{ $row[2] }}</td><td>{{ $cell('plan', $index, 0) }}</td></tr>
      @endforeach
    </table>

    <h2>5. Start Low, Go Slow</h2>
    <p>Do not do it all at once. Build up step by step.</p>
    <table>
      <tr><th>Weeks</th><th>What to do</th></tr>
      <tr><td>Weeks 1 to 2</td><td>10 minutes of walking, 3 days per week</td></tr>
      <tr><td>Weeks 3 to 4</td><td>15 to 20 minutes, 3 to 4 days per week</td></tr>
      <tr><td>Weeks 5 to 8</td><td>20 to 30 minutes, 4 to 5 days per week. Add 2 strength days.</td></tr>
      <tr><td>Week 9 and after</td><td>Aim for 150 minutes per week. Mix cardio, strength and stretching.</td></tr>
    </table>
    <p>My starting point: <span class="field-line">{{ $form['startmin'] ?? '' }}</span> minutes of <span class="field-line">{{ $form['startact'] ?? '' }}</span> on <span class="field-line">{{ $form['startdays'] ?? '' }}</span> days per week.</p>

    <h2>6. How Hard Should I Work? (The Talk Test)</h2>
    <table>
      <tr><th>Level</th><th>How it feels</th><th>Talk test</th></tr>
      <tr><td>Easy</td><td>Light effort</td><td>You can sing</td></tr>
      <tr><td>Moderate (aim for this)</td><td>Slightly breathless, warm</td><td>You can talk but not sing</td></tr>
      <tr><td>Hard</td><td>Very breathless</td><td>You can say only a few words</td></tr>
    </table>
    <p>My target: Moderate. Warm up for 5 minutes first and cool down for 5 minutes after.</p>

    <h2>7. My Weekly Schedule</h2>
    <table>
      <tr><th>Day</th><th>Activity</th><th>Time of day</th><th>Minutes</th><th>Done (X)</th></tr>
      @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $index => $day)
        <tr>
          <td>{{ $day }}</td><td>{{ $cell('schedule', $index, 0) }}</td><td>{{ $cell('schedule', $index, 1) }}</td>
          <td>{{ $cell('schedule', $index, 2) }}</td><td class="center"><span class="box">{{ $cell('schedule', $index, 3) ? 'X' : '' }}</span></td>
        </tr>
      @endforeach
    </table>

    <h2>8. Safety Rules</h2>
    <p>STOP and rest if you feel: chest pain or pressure, severe shortness of breath, dizziness or fainting, a racing or irregular heartbeat, or sharp pain in a joint or muscle. If symptoms do not go away quickly, get medical help right away.</p>
    <p>Drink water before, during and after exercise.</p>
    <p>Wear comfortable shoes and clothes.</p>
    <p>Avoid exercising in extreme heat, or right after a heavy meal.</p>
    <p>Rest at least 1 day per week.</p>
    <p>Mild muscle soreness is normal. Sharp pain is not.</p>

    <h2>9. Special Instructions From My Doctor</h2>
    @foreach ($exerciseSpecialInstructions as $option)
      <p class="choice-block">
        <span class="box">{{ $hasChoice('special', $option) ? 'X' : '' }}</span>
        {{ $option === 'Other' ? 'Other: ' . ($form['specialother'] ?? '______________________________________________') : $option }}
      </p>
    @endforeach

    <h2>10. Tips for Low Energy or Low Motivation Days</h2>
    @foreach ($exerciseTips as $tip)
      <p class="choice-block"><span class="box">{{ $hasChoice('tips', $tip) ? 'X' : '' }}</span>{{ $tip }}</p>
    @endforeach

    <h2>11. My SMART Goal This Week</h2>
    <p>"This week, I will <span class="field-line">{{ $form['sg1'] ?? '' }}</span> for <span class="field-line">{{ $form['sg2'] ?? '' }}</span> minutes, <span class="field-line">{{ $form['sg3'] ?? '' }}</span> days (how long and how often), at <span class="field-line">{{ $form['sg4'] ?? '' }}</span> (when and where)."</p>
    <p>Confidence I can do it (circle):
      @foreach (range(1, 10) as $score)
        <span class="choice">{{ ($form['conf'] ?? '') == (string) $score ? '●' : '○' }} {{ $score }}</span>
      @endforeach
    </p>
    <p>If below 7, make the goal smaller.</p>

    <h2>12. Mood and Energy Tracker</h2>
    <p>Rate from 0 (worst) to 10 (best) before and after you exercise.</p>
    <table>
      <tr><th>Date</th><th>Activity and minutes</th><th>Mood before</th><th>Mood after</th><th>Energy after</th><th>Notes</th></tr>
      @foreach (range(0, 5) as $index)
        <tr>
          @foreach (range(0, 5) as $column)
            <td>{{ $cell('tracker', $index, $column) }}</td>
          @endforeach
        </tr>
      @endforeach
    </table>

    <h2>13. Follow-Up</h2>
    <p>Next visit: <span class="field-line">{{ $form['next'] ?? '' }}</span></p>
    <p>Contact the clinic if you have:
      @foreach ($followUpItems as $item)
        <span class="choice"><span class="box">{{ $hasChoice('contact', $item) ? 'X' : '' }}</span>{{ strtolower($item) }}</span>
      @endforeach
    </p>
    <p>In an emergency, go to the nearest hospital or call your local emergency number.</p>
    <p>Doctor's signature: <span class="signature">{{ $form['docsig'] ?? '' }}</span> &nbsp; Patient's signature: <span class="signature">{{ $form['patsig'] ?? '' }}</span></p>
  @else
    <h2>1. About You</h2>
    <table>
      <tr><th>Name</th><td colspan="3">{{ $prescription->patient_name }}</td></tr>
      <tr><th>Age / Sex</th><td colspan="3">{{ $prescription->patient_age }} / {{ $prescription->patient_sex }}</td></tr>
      <tr><th>Height / Weight / BMI</th><td colspan="3">{{ $form['hwb'] ?? '' }}</td></tr>
      <tr><th>Food allergies or intolerances</th><td colspan="3">{{ $form['allergy'] ?? '' }}</td></tr>
      <tr>
        <th>Medical conditions</th>
        <td colspan="3">
          @foreach (['Diabetes', 'High blood pressure', 'High cholesterol', 'Kidney disease', 'Other'] as $condition)
            <span class="choice"><span class="box">{{ $hasChoice('conditions', $condition) ? 'X' : '' }}</span>{{ $condition === 'Other' ? 'Other: ' . ($form['condother'] ?? '__________') : $condition }}</span>
          @endforeach
        </td>
      </tr>
      <tr><th>Current medicines</th><td colspan="3">{{ $form['meds'] ?? '' }}</td></tr>
    </table>

    <h2>2. My Main Goals (check up to 3)</h2>
    @foreach ($nutritionGoals as $goal)
      <p class="choice-block"><span class="box">{{ $hasChoice('goals', $goal) ? 'X' : '' }}</span>{{ $goal === 'Other' ? 'Other: ' . ($form['goalother'] ?? '______________________________') : $goal }}</p>
    @endforeach

    <h2>3. My Plate (every main meal)</h2>
    <p>Use this guide on every main meal.</p>
    <table>
      <tr><th>Fill this much of your plate</th><th>With</th></tr>
      <tr><td>½ plate</td><td>Vegetables and fruits (any color, fresh, frozen or canned)</td></tr>
      <tr><td>¼ plate</td><td>Whole grains or starchy foods (brown rice, oats, whole wheat bread, sweet potato, corn)</td></tr>
      <tr><td>¼ plate</td><td>Protein (fish, eggs, chicken, tofu, beans, lentils, nuts)</td></tr>
      <tr><td>Add</td><td>A small amount of healthy fat (olive oil, avocado, nuts)</td></tr>
      <tr><td>Drink</td><td>Water first. Tea or coffee is fine in moderation.</td></tr>
    </table>

    <h2>4. My Daily Targets</h2>
    <table>
      <tr><th>Food group</th><th>Goal per day</th><th>Examples of 1 serving</th></tr>
      @foreach ($nutritionTargets as $index => $target)
        <tr>
          <td>{{ $target[0] }}</td>
          <td><span class="box">{{ $cell('targets', $index, 0) ? 'X' : '' }}</span>{{ $target[1] }}</td>
          <td>{{ $target[2] }}</td>
        </tr>
      @endforeach
    </table>

    <h2>5. Eat More Of (Mood and Brain Friendly)</h2>
    @foreach ($nutritionMoreFoods as $food)
      <p class="choice-block"><span class="box">{{ $hasChoice('more', $food) ? 'X' : '' }}</span>{{ $food }}</p>
    @endforeach

    <h2>6. Eat Less Of</h2>
    @foreach ($nutritionLessFoods as $food)
      <p class="choice-block"><span class="box">{{ $hasChoice('less', $food) ? 'X' : '' }}</span>{{ $food }}</p>
    @endforeach
    <p class="choice-block"><span class="box">{{ $hasChoice('less', 'Alcohol (best: none)') ? 'X' : '' }}</span>Alcohol: limit to <span class="field-line">{{ $form['alc'] ?? '' }}</span> drinks per week (best: none)</p>
    <p class="choice-block"><span class="box">{{ $hasChoice('less', 'Caffeine (none after 2 PM)') ? 'X' : '' }}</span>Caffeine: no more than <span class="field-line">{{ $form['caf'] ?? '' }}</span> cups per day, none after 2 PM</p>

    <h2>7. Easy Swaps I Will Try</h2>
    <table>
      <tr><th>Instead of</th><th>I will choose</th></tr>
      <tr><td>White rice</td><td>Brown rice or half white, half brown</td></tr>
      <tr><td>Soda or juice</td><td>Water, sparkling water, unsweetened tea</td></tr>
      <tr><td>Chips or biscuits</td><td>Fruit, nuts, yogurt</td></tr>
      <tr><td>Fried chicken</td><td>Grilled or steamed chicken or fish</td></tr>
      <tr><td>{{ $form['swapa'] ?? '______________' }}</td><td>{{ $form['swapb'] ?? '______________' }}</td></tr>
    </table>

    <h2>8. My Eating Schedule</h2>
    <p class="choice-block"><span class="box">{{ $hasChoice('eating', 'Breakfast within 1 to 2 hours of waking') ? 'X' : '' }}</span>Breakfast within 1 to 2 hours of waking</p>
    <p class="choice-block"><span class="box">{{ $hasChoice('eating', 'Eat slowly and without screens when possible') ? 'X' : '' }}</span>Eat slowly and without screens when possible</p>
    <p class="choice-block"><span class="box">{{ filled($form['lunch'] ?? null) ? 'X' : '' }}</span>Lunch around <span class="field-line">{{ $form['lunch'] ?? '' }}</span> AM/PM</p>
    <p class="choice-block"><span class="box">{{ filled($form['dinner'] ?? null) ? 'X' : '' }}</span>Dinner around <span class="field-line">{{ $form['dinner'] ?? '' }}</span> PM, at least 2 to 3 hours before bed</p>
    <p class="choice-block"><span class="box">{{ filled($form['snack'] ?? null) ? 'X' : '' }}</span>Healthy snack (if needed): <span class="field-line">{{ $form['snack'] ?? '' }}</span></p>

    <h2>9. Special Instructions From My Doctor</h2>
    @foreach ($nutritionSpecialInstructions as $option)
      <p class="choice-block">
        <span class="box">{{ $hasChoice('special', $option) ? 'X' : '' }}</span>
        {{ $option === 'Other' ? 'Other: ' . ($form['specialother'] ?? '______________________________________________') : $option }}
      </p>
    @endforeach

    <h2>10. My SMART Goal This Week</h2>
    <p>"This week, I will <span class="field-line">{{ $form['sg1'] ?? '' }}</span> <span class="field-line">{{ $form['sg2'] ?? '' }}</span> days per week (how often) at <span class="field-line">{{ $form['sg3'] ?? '' }}</span> (when and where)."</p>
    <p>Confidence I can do it (circle):
      @foreach (range(1, 10) as $score)
        <span class="choice">{{ ($form['conf'] ?? '') == (string) $score ? '●' : '○' }} {{ $score }}</span>
      @endforeach
    </p>
    <p>If below 7, make the goal smaller.</p>

    <h2>11. Weekly Check-In</h2>
    <table>
      <tr><th>Day</th><th>Veggies (X)</th><th>Fruit (X)</th><th>Water (X)</th><th>Skipped a meal?</th><th>Mood (0 to 10)</th></tr>
      @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $index => $day)
        <tr>
          <td>{{ $day }}</td>
          <td class="center"><span class="box">{{ $cell('checkin', $index, 0) ? 'X' : '' }}</span></td>
          <td class="center"><span class="box">{{ $cell('checkin', $index, 1) ? 'X' : '' }}</span></td>
          <td class="center"><span class="box">{{ $cell('checkin', $index, 2) ? 'X' : '' }}</span></td>
          <td class="center"><span class="box">{{ $cell('checkin', $index, 3) ? 'X' : '' }}</span></td>
          <td>{{ $cell('checkin', $index, 4) }}</td>
        </tr>
      @endforeach
    </table>

    <h2>12. Follow-Up</h2>
    <p>Next visit: <span class="field-line">{{ $form['next'] ?? '' }}</span></p>
    <p>Contact the clinic if you have:
      @foreach ($followUpItems as $item)
        <span class="choice"><span class="box">{{ $hasChoice('contact', $item) ? 'X' : '' }}</span>{{ strtolower($item) }}</span>
      @endforeach
    </p>
    <p>In an emergency, go to the nearest hospital or call your local emergency number.</p>
    <p>Doctor's signature: <span class="signature">{{ $form['docsig'] ?? '' }}</span> &nbsp; Patient's signature: <span class="signature">{{ $form['patsig'] ?? '' }}</span></p>
  @endif

  <div class="footer">This prescription is general guidance and does not replace individual medical advice.</div>
</body>
</html>
