/* lifecoach_patients.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('patients');

  var PATIENTS = (LC_DATA.PATIENTS || []).slice();
  var currentPatientId = null;
  var journalEntryKind = 'setup';
  var journalChallengeStatus = new Array(30).fill(null);

  var urlParams = new URLSearchParams(window.location.search);
  var rawUrlId = urlParams.get('id');
  var urlId = rawUrlId ? String(rawUrlId).trim() : '';

  buildPatientCards();
  lcFillPatientSelect(document.getElementById('note-patient'));

  function hydratePatientDetailFromApi(patientId) {
    lcApi(lcRoute('patientsShow', patientId), { method: 'GET' })
      .then(function (data) {
        var patient = data.patient || data;
        if (!patient) {
          history.replaceState(null, '', lcRoute('patients'));
          return;
        }

        var idx = PATIENTS.findIndex(function (x) { return String(x.id) === String(patientId); });
        if (idx >= 0) {
          PATIENTS[idx] = Object.assign({}, PATIENTS[idx], patient);
        } else {
          PATIENTS.push(patient);
        }

        buildPatientCards();
        openPatientDetail(patientId);
      })
      .catch(function (err) {
        lcToast(err.message || 'Could not reload that patient detail.');
        history.replaceState(null, '', lcRoute('patients'));
      });
  }

  function buildPatientCards() {
    var grid = document.getElementById('lc-patients-grid');
    if (!grid) return;
    grid.innerHTML = '';
    var badge = document.getElementById('patient-count-badge');
    if (badge) badge.textContent = PATIENTS.length + (PATIENTS.length === 1 ? ' patient' : ' patients');

    if (!PATIENTS.length) {
      grid.innerHTML = '<div style="padding:24px;color:#94a3b8;font-size:13px;">No patients assigned to you yet. A psychiatrist can assign patients from their workspace.</div>';
      return;
    }

    PATIENTS.forEach(function (p) {
      var accentClass = p.status === 'Active' ? 'accent-active' : p.status === 'Critical' ? 'accent-critical' : 'accent-inactive';
      var div = document.createElement('div');
      div.className = 'lc-patient-card';
      div.innerHTML =
        '<div class="lc-patient-card-accent ' + accentClass + '"></div>' +
        '<div class="lc-patient-card-body">' +
        '<div class="lc-pc-top">' +
        '<div><div class="lc-pc-name">' + lcEscape(p.name) + '</div><div class="lc-pc-id">' + lcEscape(p.patient_id || p.id) + '</div></div>' +
        lcStatusBadge(p.status || 'Active') +
        '</div>' +
        '<div class="lc-pc-details">' +
        '<div class="lc-pc-detail-row"><span class="lc-pc-detail-label">Age</span><span class="lc-pc-detail-val">' + lcEscape(p.age || '—') + '</span></div>' +
        '<div class="lc-pc-detail-row"><span class="lc-pc-detail-label">Chief Complaint</span><span class="lc-pc-detail-val">' + lcEscape(p.complaint || '—') + '</span></div>' +
        '<div class="lc-pc-detail-row"><span class="lc-pc-detail-label">Next Appointment</span><span class="lc-pc-detail-val">' + lcEscape(p.nextAppt || '—') + '</span></div>' +
        '</div>' +
        '<button type="button" class="btn-green-full" data-open-patient="' + p.id + '">View Details</button>' +
        '</div>';
      grid.appendChild(div);
    });
  }

  var patientsGrid = document.getElementById('lc-patients-grid');
  if (patientsGrid) {
    patientsGrid.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-open-patient]');
      if (btn) openPatientDetail(btn.getAttribute('data-open-patient'));
    });
  }

  window.openPatientDetail = function (id) {
    var stableId = id === undefined || id === null ? '' : String(id).trim();
    if (!stableId) {
      lcShow(document.getElementById('patient-list-view'));
      lcHide(document.getElementById('patient-detail-view'));
      history.replaceState(null, '', lcRoute('patients'));
      return;
    }

    var p = PATIENTS.find(function (x) { return String(x.id) === String(stableId); });
    if (!p) {
      lcShow(document.getElementById('patient-list-view'));
      lcHide(document.getElementById('patient-detail-view'));
      history.replaceState(null, '', lcRoute('patients'));
      lcToast('That patient could not be opened from the current data snapshot.');
      return;
    }

    currentPatientId = stableId;

    lcHide(document.getElementById('patient-list-view'));
    lcShow(document.getElementById('patient-detail-view'));

    document.getElementById('pd-avatar').textContent = lcInitials(p.name);
    document.getElementById('pd-name').textContent = p.name;
    document.getElementById('pd-meta').textContent = (p.patient_id || p.id) + ' · ' + (p.status || 'Active') + ' · Age ' + (p.age || '—');
    document.getElementById('page-title').textContent = 'Patient: ' + p.name;

    var selectedUrl = lcRoute('patients') + '?id=' + encodeURIComponent(stableId);
    history.replaceState(null, '', selectedUrl);

    switchPatientTab('overview');
    populateDetail(p);
    lcRi();
  };

  var backBtn = document.getElementById('patient-back-btn');
  if (backBtn) {
    backBtn.addEventListener('click', function () {
      lcHide(document.getElementById('patient-detail-view'));
      lcShow(document.getElementById('patient-list-view'));
      document.getElementById('page-title').textContent = 'Assigned Patients';
      history.replaceState(null, '', lcRoute('patients'));
    });
  }

  window.switchPatientTab = function (tab) {
    document.querySelectorAll('.tab-btn[data-ptab]').forEach(function (b) { b.classList.remove('active'); });
    document.querySelectorAll('.ptab-panel').forEach(function (panel) { panel.classList.remove('active'); });
    var btn = document.querySelector('[data-ptab="' + tab + '"]');
    var panel = document.getElementById('ptab-' + tab);
    if (btn) btn.classList.add('active');
    if (panel) panel.classList.add('active');
    lcRi();
  };

  document.querySelectorAll('.tab-btn[data-ptab]').forEach(function (btn) {
    btn.addEventListener('click', function () { switchPatientTab(this.getAttribute('data-ptab')); });
  });

  document.querySelectorAll('[data-journal-context]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var selected = this.getAttribute('data-journal-context');
      document.querySelectorAll('[data-journal-context]').forEach(function (tab) {
        tab.classList.toggle('active', tab === btn);
        tab.setAttribute('aria-selected', tab === btn ? 'true' : 'false');
      });
      document.querySelectorAll('[data-journal-panel]').forEach(function (panel) {
        panel.classList.toggle('active', panel.getAttribute('data-journal-panel') === selected);
      });
      lcRi();
    });
  });

  var journalStartButton = document.getElementById('journal-start-btn');
  var journalReflectionButton = document.getElementById('journal-new-reflection-btn');
  if (journalStartButton) journalStartButton.addEventListener('click', function () { openJournalForm('setup'); });
  if (journalReflectionButton) journalReflectionButton.addEventListener('click', function () { openJournalForm('review'); });

  function journalValue(name) {
    var field = document.getElementById('journal-' + name);
    return field ? field.value.trim() : '';
  }

  function journalField(name, label, placeholder, type) {
    var id = 'journal-' + name;
    var input = type === 'textarea'
      ? '<textarea id="' + id + '" class="journal-input" rows="3" maxlength="1500" placeholder="' + lcEscape(placeholder || '') + '"></textarea>'
      : '<input id="' + id + '" class="journal-input" type="' + (type || 'text') + '" maxlength="500" placeholder="' + lcEscape(placeholder || '') + '">';
    return '<div class="journal-field"><label for="' + id + '">' + lcEscape(label) + '</label>' + input + '</div>';
  }

  function journalCalendarMarkup(statuses, interactive) {
    var statusList = Array.isArray(statuses) ? statuses : [];
    var columns = window.matchMedia('(max-width: 640px)').matches ? 5 : 10;
    var rowCount = Math.ceil(30 / columns);
    var html = '<div class="journal-calendar" role="grid" aria-label="30-day challenge calendar">';
    for (var row = 0; row < rowCount; row++) {
      var firstDay = row * columns;
      var lastDay = Math.min(firstDay + columns, 30);
      var reversed = row % 2 === 1;
      html += '<div class="journal-calendar-week' + (reversed ? ' is-reversed' : '') + '" role="row"><div class="journal-calendar-days">';
      for (var step = 0; step < lastDay - firstDay; step++) {
        var dayIndex = firstDay + step;
        var state = statusList[dayIndex] === 'met' || statusList[dayIndex] === 'not_met' ? statusList[dayIndex] : 'unmarked';
        var stateLabel = state === 'met' ? 'Met' : state === 'not_met' ? 'Not met' : 'Not marked';
        var dayNumber = dayIndex + 1;
        if (interactive) {
          html += '<button type="button" class="journal-calendar-day is-' + (state === 'not_met' ? 'not-met' : state) + '" role="gridcell" data-challenge-day="' + dayIndex + '" aria-label="Day ' + dayNumber + ': ' + stateLabel + '. Activate to cycle status." aria-pressed="' + (state !== 'unmarked') + '" title="Day ' + dayNumber + ': ' + stateLabel + '"><span class="journal-calendar-day-number">' + dayNumber + '</span><span class="journal-calendar-day-mark" aria-hidden="true">' + (state === 'met' ? '✓' : state === 'not_met' ? '×' : '·') + '</span></button>';
        } else {
          html += '<span class="journal-calendar-day is-' + (state === 'not_met' ? 'not-met' : state) + '" role="gridcell" aria-label="Day ' + dayNumber + ': ' + stateLabel + '" title="Day ' + dayNumber + ': ' + stateLabel + '"><span class="journal-calendar-day-number">' + dayNumber + '</span><span class="journal-calendar-day-mark" aria-hidden="true">' + (state === 'met' ? '✓' : state === 'not_met' ? '×' : '·') + '</span></span>';
        }
      }
      html += '</div></div>';
    }
    return html + '</div>';
  }

  function journalCalendarCounts(statuses) {
    var list = Array.isArray(statuses) ? statuses : [];
    var met = list.filter(function (state) { return state === 'met'; }).length;
    var notMet = list.filter(function (state) { return state === 'not_met'; }).length;
    return { met: met, notMet: notMet, unmarked: Math.max(0, 30 - met - notMet) };
  }

  function openJournalForm(kind) {
    if (!currentPatientId) return;
    journalEntryKind = kind;
    var body = document.getElementById('journal-form-fields');
    var title = document.getElementById('journal-modal-title');
    var saveButton = document.getElementById('journal-save-btn');
    if (!body || !title || !saveButton) return;

    var p = PATIENTS.find(function (patient) { return String(patient.id) === String(currentPatientId); });
    var patientEntries = p && p.journalEntries || [];
    var setup = patientEntries.find(function (entry) { return entry.kind === 'setup'; });
    var latestReview = patientEntries.find(function (entry) { return entry.kind === 'review'; });
    var previousDailyStatus = latestReview && latestReview.content && latestReview.content.challenge && latestReview.content.challenge.daily_status;
    journalChallengeStatus = Array.from({ length: 30 }, function (_, index) {
      var state = Array.isArray(previousDailyStatus) ? previousDailyStatus[index] : null;
      return state === 'met' || state === 'not_met' ? state : null;
    });
    var html = '<div class="journal-form-intro">' + (kind === 'setup'
      ? 'Use the worksheets as a conversation guide. Capture the patient’s own language; skip anything that does not fit.'
      : 'Record a brief coach-led check-in. Keep the focus on what changed, what was difficult, and one next step.') + '</div>';
    html += '<nav class="journal-step-nav" aria-label="Journal form sections">';
    if (kind === 'setup') {
      html += '<button type="button" class="active" data-journal-step="journal-step-intentions">Intentions</button><button type="button" data-journal-step="journal-step-pillars">Six pillars</button><button type="button" data-journal-step="journal-step-recovery">Recovery goals</button><button type="button" data-journal-step="journal-step-challenge">30-day challenge</button>';
    } else {
      html += '<button type="button" class="active" data-journal-step="journal-step-reflection">Reflection</button><button type="button" data-journal-step="journal-step-review-goals">Goals &amp; week</button><button type="button" data-journal-step="journal-step-review-challenge">Challenge</button>';
    }
    html += '</nav>';
    var localToday = new Date();
    localToday.setMinutes(localToday.getMinutes() - localToday.getTimezoneOffset());
    html += '<div class="journal-field journal-date-field"><label for="journal-entry-date">Entry date</label><input id="journal-entry-date" class="journal-input" type="date" value="' + localToday.toISOString().slice(0, 10) + '"></div>';

    if (kind === 'setup') {
      title.textContent = 'Journey setup';
      saveButton.textContent = 'Save journey setup';
      html += '<section class="journal-form-section" id="journal-step-intentions"><div class="journal-form-section-heading"><span>01</span><div><h4>Intentions for this journey</h4><p>What matters to the patient, and how will success feel?</p></div></div>';
      for (var i = 1; i <= 3; i++) html += journalField('intention-' + i, 'Intention ' + i, 'A goal that feels meaningful', 'text');
      html += journalField('why', 'Why do these goals matter?', 'What makes this important right now?', 'textarea');
      html += journalField('success', 'What would success look like?', 'Describe a change the patient would notice.', 'textarea') + '</section>';

      var pillars = [
        ['nutrition', 'Nutrition'], ['activity', 'Physical activity'], ['sleep', 'Restorative sleep'],
        ['stress', 'Stress management'], ['connection', 'Social connection'], ['safety', 'Avoiding addictive / risky behaviors']
      ];
      html += '<section class="journal-form-section" id="journal-step-pillars"><div class="journal-form-section-heading"><span>02</span><div><h4>Six pillars snapshot</h4><p>Capture the current picture, desired direction, and one small step.</p></div></div><div class="journal-pillar-list">';
      pillars.forEach(function (pillar) {
        html += '<div class="journal-pillar-row"><strong>' + lcEscape(pillar[1]) + '</strong>' +
          '<div>' + journalField('pillar-' + pillar[0] + '-now', 'Now', 'Where things are today') + '</div>' +
          '<div>' + journalField('pillar-' + pillar[0] + '-wish', 'Wish', 'Where they would like to be') + '</div>' +
          '<div>' + journalField('pillar-' + pillar[0] + '-step', 'One small step', 'A doable next action') + '</div></div>';
      });
      html += '</div></section>';

      var areas = [['body', 'Body'], ['mind', 'Mind'], ['people', 'People'], ['spirit', 'Spirit'], ['habits', 'Lifestyle habits']];
      html += '<section class="journal-form-section" id="journal-step-recovery"><div class="journal-form-section-heading"><span>03</span><div><h4>Goals in health &amp; recovery</h4><p>Connect a whole-person goal to a sign of progress.</p></div></div>';
      html += journalField('recovery-vision', 'Where do you want this journey to take you?', 'A hopeful direction, in the patient’s words.', 'textarea');
      areas.forEach(function (area) {
        html += '<div class="journal-recovery-row"><strong>' + lcEscape(area[1]) + '</strong>' +
          journalField('recovery-' + area[0] + '-goal', 'My healing goal', 'What would I like to work toward?') +
          journalField('recovery-' + area[0] + '-sign', 'How I’ll know it is working', 'What small change could I notice?') + '</div>';
      });
      html += '</section>';

      html += '<section class="journal-form-section" id="journal-step-challenge"><div class="journal-form-section-heading"><span>04</span><div><h4>30-day challenge</h4><p>Choose a small, specific habit. Progress is reviewed later; no streak is treated as a grade.</p></div></div>';
      html += journalField('challenge-habit', 'New habit', 'A tiny action to practice for 30 days');
      html += journalField('challenge-why', 'Why is this important?', 'What value or goal does it support?', 'textarea');
      html += journalField('challenge-strengths', 'Strengths', 'What could help?');
      html += journalField('challenge-barriers', 'Possible barriers', 'What might get in the way?');
      html += journalField('challenge-reward', 'Reward', 'A kind way to acknowledge the effort.');
      html += '<div class="journal-field"><label for="journal-cadence">Reflection cadence</label><select id="journal-cadence" class="journal-input"><option value="weekly">Weekly</option><option value="per_session">Every coaching session</option><option value="monthly">Monthly</option></select></div></section>';
    } else {
      title.textContent = 'Journal reflection';
      saveButton.textContent = 'Save reflection';
      html += '<section class="journal-form-section" id="journal-step-reflection"><div class="journal-form-section-heading"><span><i data-feather="feather"></i></span><div><h4>Pause and reflect</h4><p>Use these prompts as a gentle check-in, not a scorecard.</p></div></div>';
      html += journalField('reflection-went-well', 'What went well since the last check-in?', 'Notice effort, support, or a small win.', 'textarea');
      html += journalField('reflection-hard', 'What felt difficult?', 'What would be helpful to understand?', 'textarea');
      html += journalField('reflection-learned', 'What did you learn?', 'Anything surprising or useful?', 'textarea');
      html += journalField('reflection-next-step', 'One next step', 'Keep it small and realistic.', 'textarea') + '</section>';
      html += '<section class="journal-form-section" id="journal-step-review-goals"><div class="journal-form-section-heading"><span><i data-feather="target"></i></span><div><h4>Goals &amp; weekly practice</h4><p>Mark the days each goal was practiced this week. Your check-ins and progress will be saved with this journal reflection.</p></div></div>';
      var reviewGoals = p && Array.isArray(p.goals) ? p.goals : [];
      if (reviewGoals.length) {
        html += '<div class="journal-review-goals">' + reviewGoals.map(function (goal) {
          var weekly = Array.isArray(goal.weekly_checkins) ? goal.weekly_checkins : [];
          var checkedDays = weekly.filter(Boolean).length;
          var weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
          return '<fieldset class="journal-review-goal" data-journal-goal="' + Number(goal.id) + '"><legend><span class="journal-review-goal-icon"><i data-feather="target"></i></span><span class="journal-review-goal-title">' + lcEscape(goal.title || 'Coaching goal') + '</span><span class="journal-review-goal-category">' + lcEscape(goal.cat || 'Goal') + '</span></legend>' +
            '<div class="journal-review-goal-meta"><span>Current progress</span><strong>' + Math.max(0, Math.min(100, Number(goal.prog) || 0)) + '%</strong></div>' +
            '<div class="journal-review-goal-week" role="group" aria-label="Weekly practice for ' + lcEscape(goal.title || 'coaching goal') + '">' + weekdays.map(function (day, index) {
              var checked = weekly[index] === true;
              return '<label class="journal-weekday' + (checked ? ' is-checked' : '') + '"><input type="checkbox" class="journal-goal-check" data-day-index="' + index + '" ' + (checked ? 'checked' : '') + '><span class="journal-weekday-box" aria-hidden="true"><i data-feather="check"></i></span><span>' + day + '</span></label>';
            }).join('') + '</div><div class="journal-review-goal-foot"><span>Days practiced this week</span><strong data-goal-week-count>' + checkedDays + ' / 7</strong></div></fieldset>';
        }).join('') + '</div>';
      } else {
        html += '<div class="journal-review-goals-empty"><span><i data-feather="target"></i></span><div><strong>No coaching goals yet</strong><p>Add a goal to include its weekly practice in future reflections.</p></div><button type="button" class="journal-inline-add-goal" data-journal-add-goal><i data-feather="plus"></i> Add goal</button></div>';
      }
      html += '</section>';
      html += '<section class="journal-form-section" id="journal-step-review-challenge"><div class="journal-form-section-heading"><span>30</span><div><h4>Challenge check-in</h4><p>' + lcEscape(setup && setup.content && setup.content.challenge && setup.content.challenge.habit || '30-day habit') + '</p></div></div>';
      html += '<div class="journal-calendar-instructions"><strong>How did each day go?</strong><span>Tap a day to cycle through not marked, met, and not met.</span></div>';
      html += '<div class="journal-calendar-legend"><span><i class="is-met"></i> Met</span><span><i class="is-not-met"></i> Not met</span><span><i class="is-unmarked"></i> Not marked</span></div>';
      html += '<div id="journal-challenge-calendar-input">' + journalCalendarMarkup(journalChallengeStatus, true) + '</div>';
      html += '<div class="journal-calendar-counts" aria-live="polite"><span class="journal-calendar-count is-met"><strong id="journal-days-met">0</strong> met</span><span class="journal-calendar-count is-not-met"><strong id="journal-days-missed">0</strong> not met</span><span class="journal-calendar-count is-unmarked"><strong id="journal-days-unmarked">30</strong> not marked</span></div>';
      html += '<div class="journal-field"><label for="journal-challenge-rating">How would you rate this challenge?</label><select id="journal-challenge-rating" class="journal-input"><option value="">Choose a rating</option><option value="1">1 — Very difficult</option><option value="2">2 — Difficult</option><option value="3">3 — Mixed</option><option value="4">4 — Going well</option><option value="5">5 — Very manageable</option></select></div>';
      html += journalField('challenge-learned', 'What did you learn from trying it?', 'Keep the learning, even if the habit changes.', 'textarea') + '</section>';
    }

    body.innerHTML = html;
    body.dataset.journalGoalCheckinsChanged = 'false';
    body.onclick = function (event) {
      var addGoalButton = event.target.closest('[data-journal-add-goal]');
      if (addGoalButton) {
        lcCloseModal('journal-modal');
        resetGoalModal();
        lcOpenModal('goal-modal');
        return;
      }
      var dayButton = event.target.closest('[data-challenge-day]');
      if (!dayButton) return;
      var dayIndex = Number(dayButton.getAttribute('data-challenge-day'));
      if (!Number.isInteger(dayIndex) || dayIndex < 0 || dayIndex >= 30) return;
      var currentState = journalChallengeStatus[dayIndex];
      journalChallengeStatus[dayIndex] = currentState === null ? 'met' : currentState === 'met' ? 'not_met' : null;
      var updatedState = journalChallengeStatus[dayIndex] || 'unmarked';
      var updatedLabel = updatedState === 'met' ? 'Met' : updatedState === 'not_met' ? 'Not met' : 'Not marked';
      dayButton.classList.remove('is-met', 'is-not-met', 'is-unmarked');
      dayButton.classList.add('is-' + (updatedState === 'not_met' ? 'not-met' : updatedState));
      dayButton.setAttribute('aria-label', 'Day ' + (dayIndex + 1) + ': ' + updatedLabel + '. Activate to cycle status.');
      dayButton.setAttribute('aria-pressed', String(updatedState !== 'unmarked'));
      dayButton.title = 'Day ' + (dayIndex + 1) + ': ' + updatedLabel;
      dayButton.querySelector('.journal-calendar-day-mark').textContent = updatedState === 'met' ? '✓' : updatedState === 'not_met' ? '×' : '·';
      var counts = journalCalendarCounts(journalChallengeStatus);
      document.getElementById('journal-days-met').textContent = counts.met;
      document.getElementById('journal-days-missed').textContent = counts.notMet;
      document.getElementById('journal-days-unmarked').textContent = counts.unmarked;
    };
    body.onchange = function (event) {
      var checkbox = event.target.closest('.journal-goal-check');
      if (!checkbox) return;
      body.dataset.journalGoalCheckinsChanged = 'true';
      var card = checkbox.closest('.journal-review-goal');
      if (!card) return;
      checkbox.closest('.journal-weekday').classList.toggle('is-checked', checkbox.checked);
      var count = card.querySelectorAll('.journal-goal-check:checked').length;
      var countLabel = card.querySelector('[data-goal-week-count]');
      if (countLabel) countLabel.textContent = count + ' / 7';
    };
    var initialChallengeCounts = journalCalendarCounts(journalChallengeStatus);
    var daysMet = document.getElementById('journal-days-met');
    var daysMissed = document.getElementById('journal-days-missed');
    var daysUnmarked = document.getElementById('journal-days-unmarked');
    if (daysMet) daysMet.textContent = initialChallengeCounts.met;
    if (daysMissed) daysMissed.textContent = initialChallengeCounts.notMet;
    if (daysUnmarked) daysUnmarked.textContent = initialChallengeCounts.unmarked;
    var ratingInput = document.getElementById('journal-challenge-rating');
    var previousRating = latestReview && latestReview.content && latestReview.content.challenge && latestReview.content.challenge.rating;
    if (ratingInput && previousRating) ratingInput.value = String(previousRating);
    body.querySelectorAll('[data-journal-step]').forEach(function (button) {
      button.addEventListener('click', function () {
        var target = document.getElementById(button.getAttribute('data-journal-step'));
        if (!target) return;
        body.querySelectorAll('[data-journal-step]').forEach(function (stepButton) {
          stepButton.classList.toggle('active', stepButton === button);
        });
        var nav = body.querySelector('.journal-step-nav');
        var bodyTop = body.getBoundingClientRect().top;
        var targetTop = target.getBoundingClientRect().top;
        var stickyOffset = nav ? nav.getBoundingClientRect().height + 14 : 14;
        body.scrollTo({
          top: body.scrollTop + targetTop - bodyTop - stickyOffset,
          behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
        });
      });
    });
    body.scrollTop = 0;
    lcOpenModal('journal-modal');
  }

  function saveJournalEntry() {
    var saveButton = document.getElementById('journal-save-btn');
    var p = PATIENTS.find(function (patient) { return String(patient.id) === String(currentPatientId); });
    if (!p || !saveButton) return;

    var content;
    var cadence = null;
    if (journalEntryKind === 'setup') {
      var intentions = [1, 2, 3].map(function (index) { return journalValue('intention-' + index); });
      var challengeHabit = journalValue('challenge-habit');
      if (!intentions.some(Boolean) && !challengeHabit) {
        lcToast('Add at least one intention or a 30-day habit to begin.');
        return;
      }
      var pillarKeys = ['nutrition', 'activity', 'sleep', 'stress', 'connection', 'safety'];
      var pillars = {};
      pillarKeys.forEach(function (key) {
        pillars[key] = {
          now: journalValue('pillar-' + key + '-now'),
          wish: journalValue('pillar-' + key + '-wish'),
          step: journalValue('pillar-' + key + '-step')
        };
      });
      var areaKeys = ['body', 'mind', 'people', 'spirit', 'habits'];
      var areas = {};
      areaKeys.forEach(function (key) {
        areas[key] = {
          goal: journalValue('recovery-' + key + '-goal'),
          sign: journalValue('recovery-' + key + '-sign')
        };
      });
      content = {
        intentions: intentions,
        why: journalValue('why'),
        success: journalValue('success'),
        pillars: pillars,
        recovery: { vision: journalValue('recovery-vision'), areas: areas },
        challenge: {
          habit: challengeHabit,
          why: journalValue('challenge-why'),
          strengths: journalValue('challenge-strengths'),
          barriers: journalValue('challenge-barriers'),
          reward: journalValue('challenge-reward')
        }
      };
      cadence = document.getElementById('journal-cadence').value;
    } else {
      var reflections = {
        went_well: journalValue('reflection-went-well'),
        hard: journalValue('reflection-hard'),
        learned: journalValue('reflection-learned'),
        next_step: journalValue('reflection-next-step')
      };
      var hasDailyStatus = journalChallengeStatus.some(function (state) { return state === 'met' || state === 'not_met'; });
      var challengeRating = Number(document.getElementById('journal-challenge-rating').value || 0) || null;
      var challengeLearning = journalValue('challenge-learned');
      var goalCheckins = Array.from(body.querySelectorAll('.journal-review-goal')).map(function (card) {
        return {
          goal_id: Number(card.getAttribute('data-journal-goal')),
          weekly_checkins: Array.from(card.querySelectorAll('.journal-goal-check')).map(function (checkbox) { return checkbox.checked; })
        };
      });
      var hasWeeklyPractice = goalCheckins.some(function (goal) { return goal.weekly_checkins.some(Boolean); });
      var goalCheckinsChanged = body.dataset.journalGoalCheckinsChanged === 'true';
      if (!Object.keys(reflections).some(function (key) { return Boolean(reflections[key]); }) && !hasDailyStatus && !challengeRating && !challengeLearning && !hasWeeklyPractice && !goalCheckinsChanged) {
        lcToast('Add a reflection before saving.');
        return;
      }
      content = {
        reflection: reflections,
        goal_checkins: goalCheckins,
        challenge: {
          days_practiced: journalCalendarCounts(journalChallengeStatus).met,
          daily_status: journalChallengeStatus.slice(),
          rating: challengeRating,
          learned: challengeLearning
        }
      };
    }

    saveButton.disabled = true;
    lcApi(lcRoute('journalEntriesStore', currentPatientId), {
      method: 'POST',
      body: JSON.stringify({
        kind: journalEntryKind,
        cadence: cadence,
        entry_date: document.getElementById('journal-entry-date').value,
        content: content
      })
    }).then(function (data) {
      p.journalEntries = p.journalEntries || [];
      p.journalEntries.unshift(data.entry);
      var savedGoalCheckins = data.entry && data.entry.content && data.entry.content.goal_checkins || [];
      savedGoalCheckins.forEach(function (snapshot) {
        var goal = (p.goals || []).find(function (item) { return String(item.id) === String(snapshot.goal_id); });
        if (!goal) return;
        goal.weekly_checkins = snapshot.weekly_checkins;
        goal.prog = Number(snapshot.progress) || 0;
      });
      populateDetail(p);
      lcCloseModal('journal-modal');
      lcToast(data.message || 'Journal entry saved.');
    }).catch(function (error) {
      lcToast(error.message || 'Could not save this journal entry.');
    }).finally(function () {
      saveButton.disabled = false;
    });
  }

  var journalSaveButton = document.getElementById('journal-save-btn');
  if (journalSaveButton) journalSaveButton.addEventListener('click', saveJournalEntry);

  var journalTimeline = document.getElementById('journal-timeline');
  if (journalTimeline) {
    journalTimeline.addEventListener('click', function (event) {
      var tab = event.target.closest('[data-journal-entry-tab]');
      if (!tab) return;
      var entry = tab.closest('.journal-entry-content');
      if (!entry) return;
      var selectedPanelId = tab.getAttribute('aria-controls');
      entry.querySelectorAll('[data-journal-entry-tab]').forEach(function (item) {
        var selected = item === tab;
        item.classList.toggle('active', selected);
        item.setAttribute('aria-selected', String(selected));
        item.tabIndex = selected ? 0 : -1;
      });
      entry.querySelectorAll('.journal-entry-tab-panel').forEach(function (panel) {
        var selected = panel.id === selectedPanelId;
        panel.hidden = !selected;
        panel.classList.toggle('is-active', selected);
      });
    });

    journalTimeline.addEventListener('keydown', function (event) {
      var tab = event.target.closest('[data-journal-entry-tab]');
      if (!tab) return;
      var tabList = tab.closest('[role="tablist"]');
      if (!tabList) return;
      var tabs = Array.from(tabList.querySelectorAll('[data-journal-entry-tab]'));
      var index = tabs.indexOf(tab);
      var nextIndex = event.key === 'ArrowRight' || event.key === 'ArrowDown' ? (index + 1) % tabs.length
        : event.key === 'ArrowLeft' || event.key === 'ArrowUp' ? (index - 1 + tabs.length) % tabs.length
          : event.key === 'Home' ? 0
            : event.key === 'End' ? tabs.length - 1
              : -1;
      if (nextIndex < 0) return;
      event.preventDefault();
      tabs[nextIndex].focus();
      tabs[nextIndex].click();
    });
  }

  function renderJournal(p) {
    var entries = p.journalEntries || [];
    var setup = entries.find(function (entry) { return entry.kind === 'setup'; });
    var status = document.getElementById('journal-status-row');
    var timeline = document.getElementById('journal-timeline');
    var setupButton = document.getElementById('journal-start-btn');
    var reflectionButton = document.getElementById('journal-new-reflection-btn');
    if (!status || !timeline) return;

    status.innerHTML = setup
      ? '<span class="journal-status-pill is-complete"><i data-feather="check-circle"></i> Journey setup complete</span><span class="journal-cadence-label">Reflections: ' + lcEscape({ weekly: 'weekly', per_session: 'every coaching session', monthly: 'monthly' }[setup.cadence] || 'weekly') + '</span>'
      : '<span class="journal-status-pill is-pending"><i data-feather="circle"></i> Journey setup not started</span><span class="journal-cadence-label">Begin with the patient’s intentions and preferred small steps.</span>';
    if (setupButton) setupButton.hidden = Boolean(setup);
    if (reflectionButton) reflectionButton.hidden = !setup;

    if (!entries.length) {
      timeline.innerHTML = '<div class="journal-empty"><span class="journal-empty-icon"><i data-feather="book-open"></i></span><h3>Your first page starts with a conversation</h3><p>Use the four worksheet-inspired sections to capture intentions, the six pillars, recovery goals, and one manageable 30-day challenge.</p></div>';
      lcRi();
      return;
    }

    var latestReview = entries.find(function (entry) { return entry.kind === 'review'; });
    var latestChallenge = latestReview && latestReview.content && latestReview.content.challenge || {};
    var latestDailyStatus = Array.isArray(latestChallenge.daily_status) ? latestChallenge.daily_status : [];

    function answer(label, value) {
      if (value === null || value === undefined || String(value).trim() === '') return '';
      return '<div class="journal-answer"><span>' + lcEscape(label) + '</span><p>' + lcEscape(value) + '</p></div>';
    }

    function panel(entryId, key, content, active) {
      var id = entryId + '-panel-' + key;
      var tabId = entryId + '-tab-' + key;
      return '<section class="journal-entry-tab-panel' + (active ? ' is-active' : '') + '" id="' + id + '" role="tabpanel" aria-labelledby="' + tabId + '" tabindex="0"' + (active ? '' : ' hidden') + '>' + content + '</section>';
    }

    function calendarCountsMarkup(statuses, legacyDays) {
      var counts = journalCalendarCounts(statuses);
      var legacy = !Array.isArray(statuses) || statuses.length !== 30;
      var summary = legacy && Number.isFinite(Number(legacyDays))
        ? '<p class="journal-calendar-note">A previous check-in recorded ' + Math.max(0, Math.min(30, Number(legacyDays))) + ' days practiced, but did not track individual dates.</p>'
        : '';
      return '<div class="journal-calendar-legend"><span><i class="is-met"></i> Met</span><span><i class="is-not-met"></i> Not met</span><span><i class="is-unmarked"></i> Not marked</span></div>' + journalCalendarMarkup(statuses, false) +
        '<div class="journal-calendar-counts"><span class="journal-calendar-count is-met"><strong>' + counts.met + '</strong> met</span><span class="journal-calendar-count is-not-met"><strong>' + counts.notMet + '</strong> not met</span><span class="journal-calendar-count is-unmarked"><strong>' + counts.unmarked + '</strong> not marked</span></div>' + summary;
    }

    timeline.innerHTML = entries.map(function (entry) {
      var content = entry.content || {};
      var isSetup = entry.kind === 'setup';
      var challenge = content.challenge || {};
      var reflection = content.reflection || {};
      var entryId = 'journal-entry-' + entry.id;
      var entryIcon = isSetup ? 'map' : 'feather';
      var tabs = [];
      var panels = '';
      var overview = '';

      if (isSetup) {
        var intentions = (content.intentions || []).filter(Boolean);
        var pillarNames = { nutrition: 'Nutrition', activity: 'Physical activity', sleep: 'Restorative sleep', stress: 'Stress management', connection: 'Social connection', safety: 'Avoiding addictive / risky behaviors' };
        var recovery = content.recovery || {};
        var areaNames = { body: 'Body', mind: 'Mind', people: 'People', spirit: 'Spirit', habits: 'Lifestyle habits' };
        var filledPillars = 0;
        var filledRecoveryAreas = 0;

        Object.keys(pillarNames).forEach(function (key) {
          var pillar = content.pillars && content.pillars[key] || {};
          if (pillar.now || pillar.wish || pillar.step) filledPillars++;
        });
        Object.keys(areaNames).forEach(function (key) {
          var area = recovery.areas && recovery.areas[key] || {};
          if (area.goal || area.sign) filledRecoveryAreas++;
        });

        var intentionContent = intentions.length
          ? '<ol class="journal-intention-list">' + intentions.map(function (item, index) { return '<li><span>' + (index + 1) + '</span><strong>' + lcEscape(item) + '</strong></li>'; }).join('') + '</ol>'
          : '<p class="journal-calendar-note">No intentions were recorded.</p>';
        intentionContent += answer('Why it matters', content.why) + answer('What success could look like', content.success);

        var pillarRows = Object.keys(pillarNames).map(function (key) {
          var pillar = content.pillars && content.pillars[key] || {};
          var values = answer('Now', pillar.now) + answer('Wish', pillar.wish) + answer('One small step', pillar.step);
          return values ? '<div class="journal-snapshot-row"><strong>' + lcEscape(pillarNames[key]) + '</strong>' + values + '</div>' : '';
        }).join('');
        var pillarsContent = pillarRows ? '<div class="journal-snapshot-list">' + pillarRows + '</div>' : '<p class="journal-calendar-note">No pillar details were recorded.</p>';

        var areaRows = Object.keys(areaNames).map(function (key) {
          var area = recovery.areas && recovery.areas[key] || {};
          var values = answer('Healing goal', area.goal) + answer('How progress may show', area.sign);
          return values ? '<div class="journal-snapshot-row"><strong>' + lcEscape(areaNames[key]) + '</strong>' + values + '</div>' : '';
        }).join('');
        var recoveryContent = answer('Journey direction', recovery.vision) + (areaRows ? '<div class="journal-snapshot-list">' + areaRows + '</div>' : '<p class="journal-calendar-note">No recovery-area details were recorded.</p>');

        var challengePlan = '<div class="journal-challenge-card"><div class="journal-challenge-icon"><i data-feather="zap"></i></div><div class="journal-challenge-copy"><span>30-day practice</span><strong>' + lcEscape(challenge.habit || 'No challenge habit recorded') + '</strong><small>Mark each day as met or not met during a check-in.</small></div><span class="journal-challenge-state">' + (challenge.habit ? 'Planned' : 'Not set') + '</span></div>';
        challengePlan += answer('Why it matters', challenge.why) + answer('Strengths', challenge.strengths) + answer('Possible barriers', challenge.barriers) + answer('Reward', challenge.reward);
        challengePlan += '<div class="journal-calendar-section"><div class="journal-calendar-section-heading"><div><h4>30-day calendar</h4><p>Color shows the latest recorded status for each day.</p></div></div>' + calendarCountsMarkup(latestDailyStatus, latestChallenge.days_practiced) + '</div>';

        tabs = [
          ['intentions', 'Intentions', 'target', intentions.length + ' / 3'],
          ['pillars', 'Six pillars', 'compass', filledPillars + ' / 6'],
          ['recovery', 'Recovery goals', 'heart', filledRecoveryAreas + ' / 5'],
          ['challenge', '30-day challenge', 'calendar', 'Calendar']
        ];
        panels = panel(entryId, 'intentions', intentionContent, true) + panel(entryId, 'pillars', pillarsContent, false) + panel(entryId, 'recovery', recoveryContent, false) + panel(entryId, 'challenge', challengePlan, false);

        var stats = [
          ['target', intentions.length + ' / 3', 'Intentions'],
          ['compass', filledPillars + ' / 6', 'Pillars explored'],
          ['heart', filledRecoveryAreas + ' / 5', 'Recovery areas']
        ];
        overview = '<div class="journal-entry-lead"><div><span class="journal-entry-lead-label">The journey begins with</span><p>' + lcEscape(intentions[0] || recovery.vision || 'A conversation about what matters most') + '</p></div>' + (challenge.habit ? '<span class="journal-plan-chip"><i data-feather="activity"></i> 30-day challenge planned</span>' : '') + '</div>';
        overview += '<div class="journal-entry-stats">' + stats.map(function (item) { return '<div class="journal-entry-stat"><span class="journal-entry-stat-icon"><i data-feather="' + item[0] + '"></i></span><strong>' + item[1] + '</strong><span>' + item[2] + '</span></div>'; }).join('') + '</div>';
        if (challenge.habit) {
          overview += '<div class="journal-challenge-card"><div class="journal-challenge-icon"><i data-feather="zap"></i></div><div class="journal-challenge-copy"><span>30-day practice</span><strong>' + lcEscape(challenge.habit) + '</strong><small>One small step, practiced with curiosity—not perfection.</small></div><span class="journal-challenge-state">Planned</span></div>';
        }
      } else {
        var dailyStatus = Array.isArray(challenge.daily_status) ? challenge.daily_status : [];
        var counts = journalCalendarCounts(dailyStatus);
        var hasIndividualDays = dailyStatus.length === 30;
        var practiced = hasIndividualDays ? counts.met : Math.min(30, Math.max(0, Number(challenge.days_practiced) || 0));
        var progressPercent = Math.round((practiced / 30) * 100);
        var rating = Math.min(5, Math.max(0, Number(challenge.rating) || 0));
        var goalCheckins = Array.isArray(content.goal_checkins) ? content.goal_checkins : [];
        var weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        var reflectionContent = answer('What went well', reflection.went_well) + answer('What felt difficult', reflection.hard) + answer('What was learned', reflection.learned) + answer('One next step', reflection.next_step);
        if (!reflectionContent) reflectionContent = '<p class="journal-calendar-note">No reflection details were recorded.</p>';
        var challengeContent = '<div class="journal-calendar-section"><div class="journal-calendar-section-heading"><div><h4>30-day challenge calendar</h4><p>' + lcEscape(setup && setup.content && setup.content.challenge && setup.content.challenge.habit || 'Challenge practice') + '</p></div></div>' + calendarCountsMarkup(dailyStatus, challenge.days_practiced) + '</div>';
        challengeContent += answer('What did the challenge teach you?', challenge.learned);
        var goalsContent = goalCheckins.length
          ? '<div class="journal-goal-snapshot-list">' + goalCheckins.map(function (snapshot) {
            var weekly = Array.isArray(snapshot.weekly_checkins) ? snapshot.weekly_checkins : [];
            var checkedCount = weekly.filter(Boolean).length;
            return '<article class="journal-goal-snapshot"><div class="journal-goal-snapshot-top"><span class="journal-goal-snapshot-icon"><i data-feather="target"></i></span><div><strong>' + lcEscape(snapshot.title || 'Coaching goal') + '</strong><span>' + lcEscape(snapshot.category || 'Goal') + '</span></div><b>' + Math.max(0, Math.min(100, Number(snapshot.progress) || 0)) + '%</b></div><div class="journal-goal-snapshot-week" aria-label="' + checkedCount + ' of 7 days practiced">' + weekdays.map(function (day, index) {
              var checked = weekly[index] === true;
              return '<span class="journal-weekday-mark' + (checked ? ' is-checked' : '') + '" title="' + day + ': ' + (checked ? 'Practiced' : 'Not logged') + '">' + day.charAt(0) + '</span>';
            }).join('') + '<small>' + checkedCount + ' / 7 days practiced</small></div></article>';
          }).join('') + '</div>'
          : '<p class="journal-calendar-note">This reflection did not include weekly goal check-ins.</p>';
        tabs = [
          ['reflection', 'Reflection notes', 'feather', 'Journal'],
          ['challenge', 'Challenge calendar', 'calendar', (hasIndividualDays ? counts.met + ' met' : '30 days')]
        ];
        if (goalCheckins.length) tabs.splice(1, 0, ['goals', 'Goals & week', 'target', goalCheckins.length + ' goals']);
        panels = panel(entryId, 'reflection', reflectionContent, true) +
          (goalCheckins.length ? panel(entryId, 'goals', goalsContent, false) : '') +
          panel(entryId, 'challenge', challengeContent, false);

        overview = '<div class="journal-entry-lead journal-entry-lead--review"><div><span class="journal-entry-lead-label">A moment worth noticing</span><p>' + lcEscape(reflection.went_well || reflection.learned || reflection.next_step || 'A reflection was recorded for this check-in.') + '</p></div>' + (rating ? '<div class="journal-rating" role="img" aria-label="Challenge rating ' + rating + ' out of 5"><span>' + '★'.repeat(rating) + '<span class="journal-rating-empty">' + '☆'.repeat(5 - rating) + '</span></span><small>' + rating + ' / 5</small></div>' : '') + '</div>';
        overview += '<div class="journal-progress-card"><div class="journal-progress-top"><span><i data-feather="activity"></i> 30-day challenge progress</span><strong>' + practiced + '<small> / 30 days met</small></strong></div><div class="journal-progress-track" role="progressbar" aria-label="Challenge days met" aria-valuemin="0" aria-valuemax="30" aria-valuenow="' + practiced + '"><span style="width:' + progressPercent + '%"></span></div><div class="journal-progress-foot"><span>' + progressPercent + '% met</span><span>' + (practiced >= 30 ? 'Challenge complete' : (30 - practiced) + ' days remaining') + '</span></div></div>';
        if (goalCheckins.length) overview += '<div class="journal-review-summary"><i data-feather="target"></i><span><strong>' + goalCheckins.length + ' goal' + (goalCheckins.length === 1 ? '' : 's') + '</strong> reviewed with weekly practice</span></div>';
      }

      var tabsMarkup = '<div class="journal-entry-tabs" role="tablist" aria-label="' + (isSetup ? 'Journey setup sections' : 'Reflection sections') + '">' + tabs.map(function (item, index) {
        var tabId = entryId + '-tab-' + item[0];
        var panelId = entryId + '-panel-' + item[0];
        var active = index === 0;
        return '<button type="button" id="' + tabId + '" class="journal-entry-tab' + (active ? ' active' : '') + '" role="tab" aria-selected="' + active + '" aria-controls="' + panelId + '" tabindex="' + (active ? '0' : '-1') + '" data-journal-entry-tab><i data-feather="' + item[2] + '"></i><span>' + item[1] + '</span><small>' + item[3] + '</small></button>';
      }).join('') + '</div>';

      return '<article class="journal-entry journal-entry--' + (isSetup ? 'setup' : 'review') + '"><div class="journal-entry-marker"><span><i data-feather="' + entryIcon + '"></i></span></div><div class="journal-entry-content"><div class="journal-entry-top"><div class="journal-entry-title-wrap"><span class="journal-entry-icon"><i data-feather="' + entryIcon + '"></i></span><div><span class="journal-entry-kind">' + (isSetup ? 'Journey setup' : 'Coach reflection') + '</span><time><i data-feather="calendar"></i>' + lcEscape(entry.date || '') + '</time></div></div><span class="journal-entry-number">' + (isSetup ? 'FOUNDATION' : 'CHECK-IN') + '</span></div>' + overview + tabsMarkup + '<div class="journal-entry-tab-panels">' + panels + '</div></div></article>';
    }).join('');
    lcRi();
  }

  function populateDetail(p) {
    var infoList = document.getElementById('pd-info-list');
    if (infoList) {
      var employmentStatus = p.employment_status === 'NA'
        ? 'Not Applicable'
        : (p.employment_status ? p.employment_status.charAt(0).toUpperCase() + p.employment_status.slice(1) : '—');
      var fields = [
        ['Patient ID', p.patient_id || p.id],
        ['Status', lcStatusBadge(p.status || 'Active')],
        ['Age / Sex', (p.age || '—') + ' · ' + (p.sex || '—')],
        ['Employment Status', employmentStatus],
        ['Chief Complaint', p.complaint || '—'],
        ['Program', p.program || '—'],
        ['Coach', p.coach || '—'],
        ['Next Appt', p.nextAppt || '—'],
      ];
      infoList.innerHTML = fields.map(function (f) {
        return '<div class="pi-row"><span class="pi-label">' + f[0] + '</span><span class="pi-val">' + f[1] + '</span></div>';
      }).join('');
    }

    var rxList = document.getElementById('pd-rx-list');
    if (rxList) {
      if (!(p.prescriptions || []).length) {
        rxList.innerHTML = '<div style="padding:12px;color:#94a3b8;font-size:13px;">No active prescriptions.</div>';
      } else {
        rxList.innerHTML = p.prescriptions.map(function (rx) {
          return '<div class="rx-item"><div class="rx-item-left"><span class="rx-item-tag">' + lcEscape(rx.tag) + '</span><span class="rx-item-name">' + lcEscape(rx.name) + '</span></div><span class="rx-active-badge">Active</span></div>';
        }).join('');
      }
    }

    var recentNotes = document.getElementById('pd-recent-notes');
    if (recentNotes) {
      if (!(p.notes || []).length) {
        recentNotes.innerHTML = '<div style="padding:12px;color:#94a3b8;font-size:13px;">No coaching notes yet.</div>';
      } else {
        recentNotes.innerHTML = p.notes.slice(0, 2).map(function (n) {
          return '<div class="pd-note-row"><div class="pd-note-top"><span class="pd-note-type">' + lcEscape(n.type) + '</span><span class="pd-note-date">' + lcEscape(n.date) + '</span></div><div class="pd-note-text">' + lcEscape(n.text) + '</div></div>';
        }).join('');
      }
    }

    var metList = document.getElementById('pd-metrics-list');
    if (metList) {
      if (!(p.metrics || []).length) {
        metList.innerHTML = '<div style="padding:12px;color:#94a3b8;font-size:13px;">No lifestyle assessment on file.</div>';
      } else {
        metList.innerHTML = p.metrics.map(function (m) {
          return '<div class="metric-item"><div class="metric-icon" style="background:#f0fdf4;color:#16a34a;"><i data-feather="' + m.icon + '"></i></div><div class="metric-body"><div class="metric-label-row"><span class="metric-name">' + lcEscape(m.name) + '</span><span class="metric-value ' + m.val + '">' + lcEscape(m.value) + '</span></div><div class="metric-track"><div class="metric-bar ' + m.bar + '" style="width:' + m.pct + '%"></div></div></div></div>';
        }).join('');
      }
    }

    buildGoals(p);
    buildPatientNotes(p);
    renderJournal(p);
  }

  // Intake form read-only modal (merged from lifecoach_patients.js)
  var viewIntakeBtn = document.getElementById('view-intake-btn');
  if (viewIntakeBtn) {
    viewIntakeBtn.addEventListener('click', function () {
      var p = PATIENTS.find(function (x) { return String(x.id) === String(currentPatientId); });
      if (!p) return;
      buildIntakeModal(p.intake || {});
      lcOpenModal('intake-modal');
    });
  }

  function buildIntakeModal(intake) {
    var body = document.getElementById('intake-modal-body');
    if (!body) return;

    function row(label, value) {
      var longField = String(label || '').length > 22 || String(value || '').length > 100;
      return '<div class="intake-field' + (longField ? ' intake-field--full' : '') + '"><label class="intake-field-label">' + lcEscape(label) + '</label><div class="intake-readonly">' + lcEscape(value || '—') + '</div></div>';
    }

    function section(title, html) {
      return '<div class="intake-section"><div class="intake-section-title">' + lcEscape(title) + '</div><div class="intake-form-grid">' + html + '</div></div>';
    }

    function tags(arr) {
      if (!arr || !arr.length) return '<span class="intake-empty">None reported</span>';
      return '<div class="intake-check-grid">' + arr.map(function (t) {
        return '<span class="intake-tag">' + lcEscape(t) + '</span>';
      }).join('') + '</div>';
    }

    var personalHtml = '';
    (intake.personal || []).forEach(function (f) { personalHtml += row(f.label, f.value); });
    var recordHtml = section('Personal Information', personalHtml || '<span class="intake-empty">No data on file.</span>');

    var condHtml =
      '<div class="intake-field intake-field--full"><label class="intake-field-label">Conditions</label><div class="intake-readonly">' + tags(intake.conditions) + '</div></div>' +
      row('Current Medications', intake.medications);
    var medicalHtml = section('Medical History', condHtml);

    var famHtml = '<div class="intake-field intake-field--full"><label class="intake-field-label">Family History</label><div class="intake-readonly">' + tags(intake.family) + '</div></div>';
    medicalHtml += section('Family History', famHtml);

    var psychHtml = '';
    (intake.psychiatric || []).forEach(function (f) { psychHtml += row(f.label, f.value); });
    var psychiatricHtml = section('Personal History', psychHtml || '<span class="intake-empty">No data on file.</span>');

    var lsHtml = '';
    (intake.lifestyle || []).forEach(function (f) { lsHtml += row(f.label, f.value); });
    var lifestyleHtml = section('Lifestyle Assessment', lsHtml || '<span class="intake-empty">No assessment on file.</span>');

    if ((intake.phq || []).length) {
      lifestyleHtml += '<div class="intake-section"><div class="intake-section-title">Mental Health &amp; Well-being</div>' +
        '<div class="intake-form-hint">Over the past 2 weeks, how often have you experienced the following?</div>' +
        '<div class="intake-table-wrap"><table class="intake-table"><thead><tr><th>Question</th><th>Response</th></tr></thead><tbody>' +
        intake.phq.map(function (item) {
          return '<tr><td>' + lcEscape(item.label) + '</td><td>' + lcEscape(item.value || '—') + '</td></tr>';
        }).join('') + '</tbody></table></div></div>';
    }

    if ((intake.substances || []).length) {
      var substanceHtml = '';
      intake.substances.forEach(function (item) { substanceHtml += row(item.label, item.value); });
      lifestyleHtml += section('Substance / Habit Use', substanceHtml);
    }

    if ((intake.motivation || []).length) {
      var motivationHtml = '';
      intake.motivation.forEach(function (item) { motivationHtml += row(item.label, item.value); });
      lifestyleHtml += section('Motivation', motivationHtml);
    }

    var dietary = intake.dietary_history || {};
    var dietaryOverview = dietary.overview || (dietary.day1 || {});
    var overviewLabels = { diet: 'Usual diet', home: 'Home-cooked meals', time: 'Restaurant meals', foods: 'Food intolerances' };
    var overviewHtml = Object.keys(overviewLabels).map(function (key) {
      return '<div class="intake-dietary-overview-item"><span>' + lcEscape(overviewLabels[key]) + '</span><div>' + lcEscape(dietaryOverview[key] || '—') + '</div></div>';
    }).join('');
    var mealLabels = { breakfast: 'Breakfast', am_snack: 'AM Snack', lunch: 'Lunch', pm_snack: 'PM Snack', dinner: 'Dinner' };
    var dayKeys = ['day1', 'day2', 'day3'];
    var hasDietary = dayKeys.some(function (day) { return dietary[day] && Object.keys(dietary[day].meals || {}).length; }) || Object.keys(dietaryOverview).length;
    var dayHtml = dayKeys.map(function (day, index) {
      var meals = dietary[day] && dietary[day].meals ? dietary[day].meals : {};
      return '<article class="intake-dietary-day"><header><strong>Day ' + (index + 1) + '</strong><span>5 meal entries</span></header><div>' +
        Object.keys(mealLabels).map(function (meal) {
          return '<div class="intake-dietary-meal"><span>' + lcEscape(mealLabels[meal]) + '</span><div>' + lcEscape(meals[meal] || '—') + '</div></div>';
        }).join('') + '</div></article>';
    }).join('');
    lifestyleHtml += '<div class="intake-section intake-dietary-section"><div class="intake-section-title">3-Day Dietary History</div>' +
      (hasDietary ? '<div class="intake-dietary-overview">' + overviewHtml + '</div><div class="intake-dietary-days">' + dayHtml + '</div>' : '<span class="intake-empty">No dietary history on file.</span>') + '</div>';

    var spiritualHtml = '';
    (intake.spiritual || []).forEach(function (f) { spiritualHtml += row(f.label, f.value); });
    var interventionsHtml = '';
    (intake.interventions || []).forEach(function (f) { interventionsHtml += row(f.label, f.value); });
    var panels = {
      record: recordHtml,
      clinical: '<div class="intake-section"><div class="intake-section-title">Clinical Notes</div><div class="intake-tiptap-viewer" id="intake-clinical-notes-viewer"></div></div>',
      medical: medicalHtml,
      psychiatric: psychiatricHtml,
      lifestyle: lifestyleHtml,
      spiritual: section('Spiritual Intake', spiritualHtml || '<span class="intake-empty">No spiritual intake on file.</span>'),
      interventions: section('Therapeutic Interventions', interventionsHtml || '<span class="intake-empty">No interventions on file.</span>')
    };

    var html = Object.keys(panels).map(function (key, index) {
      return '<div class="intake-tab-panel' + (index === 0 ? ' active' : '') + '" data-intake-panel="' + key + '">' + panels[key] + '</div>';
    }).join('');

    body.innerHTML = html;
    var clinicalViewer = document.getElementById('intake-clinical-notes-viewer');
    if (clinicalViewer) clinicalViewer.innerHTML = renderTiptapDocument(intake.clinical_notes);
    document.querySelectorAll('#intake-modal [data-intake-tab]').forEach(function (tab) {
      tab.classList.toggle('active', tab.getAttribute('data-intake-tab') === 'record');
      tab.onclick = function () {
        var selected = this.getAttribute('data-intake-tab');
        document.querySelectorAll('#intake-modal [data-intake-tab]').forEach(function (item) { item.classList.toggle('active', item === tab); });
        body.querySelectorAll('[data-intake-panel]').forEach(function (panel) { panel.classList.toggle('active', panel.getAttribute('data-intake-panel') === selected); });
      };
    });
    lcRi();
  }

  function renderTiptapDocument(content) {
    if (!content) return '<span class="intake-empty">No clinical notes on file.</span>';
    try {
      while (typeof content === 'string') content = JSON.parse(content);
      if (!content || content.type !== 'doc') throw new Error('Invalid document');
      return renderTiptapNode(content);
    } catch (error) {
      return '<span class="intake-empty">No clinical notes on file.</span>';
    }
  }

  function renderTiptapNode(node) {
    var children = (node.content || []).map(renderTiptapNode).join('');
    var text = lcEscape(node.text || '');
    var attrs = node.attrs || {};
    switch (node.type) {
      case 'doc': return children;
      case 'paragraph': return '<p>' + children + '</p>';
      case 'heading':
        var level = Math.min(3, Math.max(1, Number(attrs.level) || 1));
        return '<h' + level + '>' + children + '</h' + level + '>';
      case 'bulletList': return '<ul>' + children + '</ul>';
      case 'orderedList': return '<ol>' + children + '</ol>';
      case 'listItem': return '<li>' + children + '</li>';
      case 'blockquote': return '<blockquote>' + children + '</blockquote>';
      case 'codeBlock': return '<pre><code>' + children + '</code></pre>';
      case 'hardBreak': return '<br>';
      case 'image':
        var imageSrc = String(attrs.src || '');
        if (!/^(https?:\/\/|data:image\/)/i.test(imageSrc)) return '';
        return '<img src="' + lcEscape(imageSrc) + '" alt="' + lcEscape(attrs.alt || '') + '"' + (attrs.width ? ' style="width:' + lcEscape(attrs.width) + '"' : '') + '>';
      case 'text':
        return (node.marks || []).reduce(function (value, mark) {
          if (mark.type === 'bold') return '<strong>' + value + '</strong>';
          if (mark.type === 'italic') return '<em>' + value + '</em>';
          if (mark.type === 'strike') return '<s>' + value + '</s>';
          if (mark.type === 'code') return '<code>' + value + '</code>';
          return value;
        }, text);
      default: return children || text;
    }
  }

  function openGoalModalForEdit(goalId) {
    var p = PATIENTS.find(function (x) { return String(x.id) === String(currentPatientId); });
    if (!p) return;
    var goal = (p.goals || []).find(function (g) { return String(g.id) === String(goalId); });
    if (!goal) return;

    document.getElementById('goal-title').value = goal.title || '';
    document.getElementById('goal-category').value = goal.cat || 'Mental Wellness';
    document.getElementById('goal-date').value = goal.date_raw || '';
    document.getElementById('goal-desc').value = goal.desc || '';
    document.getElementById('goal-progress').value = goal.prog || 0;

    var modalTitle = document.querySelector('#goal-modal .modal-header h3');
    if (modalTitle) modalTitle.textContent = 'Edit Coaching Goal';

    var btn = document.getElementById('save-goal-btn');
    if (btn) {
      btn.textContent = 'Update Goal';
      btn.setAttribute('data-goal-edit-id', String(goal.id));
    }

    lcOpenModal('goal-modal');
  }

  function resetGoalModal() {
    var modalTitle = document.querySelector('#goal-modal .modal-header h3');
    if (modalTitle) modalTitle.textContent = 'Add Coaching Goal';

    var btn = document.getElementById('save-goal-btn');
    if (btn) {
      btn.textContent = 'Add Goal';
      btn.removeAttribute('data-goal-edit-id');
    }

    ['goal-title', 'goal-desc', 'goal-date', 'goal-progress'].forEach(function (id) {
      var el = document.getElementById(id);
      if (el) el.value = id === 'goal-progress' ? 0 : '';
    });
  }

  function buildGoals(p) {
    var list = document.getElementById('pd-goals-list');
    if (!list) return;
    list.innerHTML = '';
    var catColors = {
      Sleep: '#2563eb', Exercise: '#16a34a', Nutrition: '#d97706',
      'Stress Management': '#dc2626', 'Mental Wellness': '#9333ea', 'Social Connection': '#0891b2',
    };
    if (!(p.goals || []).length) {
      list.innerHTML = '<div style="padding:16px;color:#94a3b8;font-size:13px;">No goals yet. Add one to get started.</div>';
      return;
    }
    p.goals.forEach(function (g) {
      var color = catColors[g.cat] || '#64748b';
      var weeklyCheckins = Array.isArray(g.weekly_checkins) ? g.weekly_checkins : [];
      var weeklyCount = weeklyCheckins.filter(Boolean).length;
      var div = document.createElement('div');
      div.className = 'goal-item';
      div.innerHTML =
        '<div class="goal-cat-dot" style="background:' + color + ';"></div>' +
        '<div class="goal-body">' +
        '<div class="goal-top"><span class="goal-title-text">' + lcEscape(g.title) + '</span><span class="goal-cat-tag" style="background:' + color + '18;color:' + color + ';border:1px solid ' + color + '30;">' + lcEscape(g.cat) + '</span></div>' +
        '<div class="goal-desc">' + lcEscape(g.desc) + '</div>' +
        '<div class="goal-date">Target: ' + lcEscape(g.date) + '</div>' +
        '<div class="goal-progress-row"><div class="goal-prog-track"><div class="goal-prog-bar" style="width:' + g.prog + '%;background:linear-gradient(90deg,' + color + ',' + color + '88);"></div></div><span class="goal-prog-pct" style="color:' + color + ';">' + g.prog + '%</span></div>' +
        '<div class="journal-goal-practice-summary"><span class="journal-goal-practice-icon"><i data-feather="calendar"></i></span><span>This week</span><strong>' + weeklyCount + ' / 7 days practiced</strong></div>' +
        '</div>' +
        '<div class="goal-actions">' +
        '<button type="button" class="btn-outline-sm" data-edit-goal="' + g.id + '">Edit</button>' +
        '<button type="button" class="btn-outline-sm danger-outline" data-delete-goal="' + g.id + '">Delete</button>' +
        '</div>';
      list.appendChild(div);
    });
    lcRi();
  }

  var addGoalBtn = document.getElementById('add-goal-btn');
  if (addGoalBtn) addGoalBtn.addEventListener('click', function () {
    resetGoalModal();
    lcOpenModal('goal-modal');
  });

  var saveGoalBtn = document.getElementById('save-goal-btn');
  if (saveGoalBtn) {
    saveGoalBtn.addEventListener('click', function () {
      var title = document.getElementById('goal-title').value.trim();
      if (!title) { lcToast('Please enter a goal title.'); return; }
      if (!currentPatientId) return;
      var cat = document.getElementById('goal-category').value;
      var date = document.getElementById('goal-date').value;
      var desc = document.getElementById('goal-desc').value.trim();
      var progress = Number(document.getElementById('goal-progress').value || 0);

      saveGoalBtn.disabled = true;

      var goalId = saveGoalBtn.getAttribute('data-goal-edit-id');
      if (goalId) {
        var req = lcApi(lcRoute('goalsUpdate', goalId), {
          method: 'PUT',
          body: JSON.stringify({
            title: title,
            category: cat,
            description: desc,
            target_date: date || null,
            progress: progress,
          }),
        });
        req.then(function (data) {
          var p = PATIENTS.find(function (x) { return String(x.id) === String(currentPatientId); });
          if (p) {
            p.goals = (p.goals || []).map(function (g) {
              if (String(g.id) === String(goalId)) return data.goal;
              return g;
            });
            populateDetail(p);
          }
          lcCloseModal('goal-modal');
          resetGoalModal();
          lcToast(data.message || 'Goal updated.');
        }).catch(function (err) {
          lcToast(err.message || 'Could not update goal.');
        }).finally(function () {
          saveGoalBtn.disabled = false;
        });
        return;
      }

      lcApi(lcRoute('goalsStore'), {
        method: 'POST',
        body: JSON.stringify({
          patient_record_id: Number(currentPatientId),
          title: title,
          category: cat,
          description: desc,
          target_date: date || null,
          progress: progress,
        }),
      }).then(function (data) {
        var p = PATIENTS.find(function (x) { return String(x.id) === String(currentPatientId); });
        if (p) {
          p.goals = p.goals || [];
          p.goals.unshift(data.goal);
          populateDetail(p);
        }
        lcCloseModal('goal-modal');
        resetGoalModal();
        lcToast(data.message || 'Goal added.');
      }).catch(function (err) {
        lcToast(err.message || 'Could not add goal.');
      }).finally(function () {
        saveGoalBtn.disabled = false;
      });
    });
  }

  document.getElementById('pd-goals-list').addEventListener('click', function (e) {
    var editButton = e.target.closest('[data-edit-goal]');
    if (editButton) {
      openGoalModalForEdit(editButton.getAttribute('data-edit-goal'));
      return;
    }

    var deleteButton = e.target.closest('[data-delete-goal]');
    if (deleteButton) {
      var goalId = deleteButton.getAttribute('data-delete-goal');
      if (!confirm('Delete this goal?')) return;
      lcApi(lcRoute('goalsDestroy', goalId), { method: 'DELETE' })
        .then(function (data) {
          var p = PATIENTS.find(function (x) { return String(x.id) === String(currentPatientId); });
          if (p) {
            p.goals = (p.goals || []).filter(function (g) { return String(g.id) !== String(goalId); });
            populateDetail(p);
          }
          lcToast(data.message || 'Goal deleted.');
        }).catch(function (err) {
          lcToast(err.message || 'Could not delete goal.');
        });
    }
  });

  function buildPatientNotes(p) {
    var list = document.getElementById('pd-notes-list');
    if (!list) return;
    list.innerHTML = '';
    if (!(p.notes || []).length) {
      list.innerHTML = '<div style="padding:16px;color:#94a3b8;font-size:13px;">No coaching notes yet.</div>';
      return;
    }
    p.notes.forEach(function (n) {
      var div = document.createElement('div');
      div.className = 'global-note-item';
      div.innerHTML =
        '<div class="gn-top"><span class="gn-patient">' + lcEscape(p.name) + '</span><div class="gn-meta"><span class="gn-type">' + lcEscape(n.type) + '</span><span class="gn-date">' + lcEscape(n.date) + '</span></div></div>' +
        '<div class="gn-text">' + lcEscape(n.text) + '</div>' +
        '<div class="gn-actions"><button type="button" class="btn-outline-sm" data-delete-note="' + n.id + '">Delete</button></div>';
      list.appendChild(div);
    });
  }

  document.getElementById('pd-notes-list').addEventListener('click', function (e) {
    var btn = e.target.closest('[data-delete-note]');
    if (!btn) return;
    var id = btn.getAttribute('data-delete-note');
    if (!confirm('Delete this note?')) return;
    lcApi(lcRoute('notesDestroy', id), { method: 'DELETE' })
      .then(function (data) {
        var p = PATIENTS.find(function (x) { return String(x.id) === String(currentPatientId); });
        if (p) {
          p.notes = (p.notes || []).filter(function (n) { return String(n.id) !== String(id); });
          buildPatientNotes(p);
          populateDetail(p);
        }
        lcToast(data.message || 'Note deleted.');
      })
      .catch(function (err) { lcToast(err.message || 'Could not delete note.'); });
  });

  var pdAddNoteBtn = document.getElementById('pd-add-note-btn');
  if (pdAddNoteBtn) {
    pdAddNoteBtn.addEventListener('click', function () {
      lcFillPatientSelect(document.getElementById('note-patient'), currentPatientId);
      lcOpenModal('note-modal');
    });
  }

  var saveNoteBtn = document.getElementById('save-note-btn');
  if (saveNoteBtn) {
    saveNoteBtn.addEventListener('click', function () {
      var patientId = document.getElementById('note-patient').value;
      var type = document.getElementById('note-type').value;
      var text = document.getElementById('note-text').value.trim();
      if (!patientId) { lcToast('Select a patient.'); return; }
      if (!text) { lcToast('Please enter a note.'); return; }

      saveNoteBtn.disabled = true;
      lcApi(lcRoute('notesStore'), {
        method: 'POST',
        body: JSON.stringify({
          patient_record_id: Number(patientId),
          session_type: type,
          body: text,
        }),
      }).then(function (data) {
        var p = PATIENTS.find(function (x) { return String(x.id) === String(patientId); });
        if (p) {
          p.notes = p.notes || [];
          p.notes.unshift(data.note);
          if (String(currentPatientId) === String(p.id)) {
            buildPatientNotes(p);
            populateDetail(p);
          }
        }
        lcCloseModal('note-modal');
        document.getElementById('note-text').value = '';
        lcToast(data.message || 'Note saved.');
      }).catch(function (err) {
        lcToast(err.message || 'Could not save note.');
      }).finally(function () {
        saveNoteBtn.disabled = false;
      });
    });
  }

  // URL deep-link — must run LAST, after openPatientDetail is defined
  if (urlId) {
    var found = PATIENTS.find(function (p) { return String(p.id) === String(urlId); });
    if (found) {
      openPatientDetail(urlId);
    } else {
      hydratePatientDetailFromApi(urlId);
    }
  } else if (urlParams.has('id')) {
    history.replaceState(null, '', lcRoute('patients'));
  }

  lcRi();
});
