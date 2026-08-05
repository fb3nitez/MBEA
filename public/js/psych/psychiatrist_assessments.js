/* psychiatrist_assessments.js — Assessments grid + 6-tab detail + save */

  /* ============================================================
     BUILD ASSESSMENTS
  ============================================================ */
  function buildAssessments(list) {
    var grid = document.getElementById('assessment-cards-grid');
    if (!grid) return;
    grid.innerHTML = '';

    var accentMap = {
      Complete: { accent: 'accent-stable', pctClass: 'pct-green', barClass: 'bar-lg-green', progClass: 'prog-green', badge: 'badge-stable', label: 'Complete' },
      'In Progress': { accent: 'accent-monitoring', pctClass: 'pct-amber', barClass: 'bar-lg-amber', progClass: 'prog-amber', badge: 'badge-monitoring', label: 'In Progress' },
      'Not Started': { accent: 'accent-maintenance', pctClass: 'pct-blue', barClass: 'bar-lg-blue', progClass: 'prog-blue', badge: 'badge-outline', label: 'Not Started' },
    };

    list.forEach(function (a) {
      var theme = accentMap[a.status] || accentMap['Not Started'];
      var badgeClass = theme.badge;
      var statusLabel = theme.label;

      var div = document.createElement('div');
      div.className = 'assess-card';
      div.setAttribute('data-assess-id', a.id);

      div.innerHTML =
        /* Top accent bar */
        '<div class="assess-card-accent ' + theme.accent + '"></div>' +

        /* Card body */
        '<div class="assess-card-body">' +

        /* Top row: ID + status badge */
        '<div class="assess-card-top">' +
        '<div class="assess-card-id-wrap">' +
        '<span class="assess-card-id">' + a.id + '</span>' +
        '<div class="assess-card-name">' + a.name + '</div>' +
        '<div class="assess-card-age">' + a.age + ' · ' + a.sex + '</div>' +
        '</div>' +
        '<span class="badge ' + badgeClass + '" style="white-space:nowrap;flex-shrink:0;">' + statusLabel + '</span>' +
        '</div>' +

        /* Diagnosis pill */
        '<div class="assess-diag-pill">' +
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>' +
        a.diag +
        '</div>' +

        /* Program tag (if any) */
        (a.tag ?
          '<div><span class="assess-prog-tag">' +
          '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:10px;height:10px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>' +
          a.tag +
          '</span></div>'
          : '') +

        /* Provider */
        '<div class="assess-provider-row">' +
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' +
        a.provider +
        '</div>' +

        /* Completion */
        '<div class="assess-completion-block">' +
        '<div class="assess-completion-header">' +
        '<span class="assess-completion-label">Assessment Completion</span>' +
        '<span class="assess-completion-pct ' + theme.pctClass + '">' + a.prog + '%</span>' +
        '</div>' +
        '<div class="assess-prog-track-lg">' +
        '<div class="assess-prog-bar-lg ' + theme.barClass + '" style="width:' + a.prog + '%"></div>' +
        '</div>' +
        '</div>' +

        '</div>' + /* /assess-card-body */

        /* Card footer */
        '<div class="assess-card-footer">' +
        '<span class="assess-last-date">' +
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' +
        'Last assessed ' + a.lastDate +
        '</span>' +
        '<button class="assess-open-btn" data-assess-open="' + a.id + '">' +
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>' +
        'Open' +
        '</button>' +
        '</div>';

      grid.appendChild(div);
    });
  }

  function populateAssessmentPatientSelects() {
    // Searchable patient pickers are initialized separately.
  }

  function populateAssessmentCards() {
    var patients = ASSESSMENT_PATIENTS || [];
    var cards = patients.map(function (p) {
      var prog = typeof p.completion === 'number' ? p.completion : (p.has_assessment ? 50 : 0);
      var status = prog >= 100 ? 'Complete' : (prog > 0 || p.has_assessment ? 'In Progress' : 'Not Started');
      return {
        id: String(p.id),
        patient_id: p.patient_id || '',
        name: p.name || 'Patient',
        age: p.age != null ? (p.age + 'y') : '—',
        sex: p.sex || '—',
        diag: p.summary || p.primary_diagnosis || 'No assessment yet',
        status: status,
        prog: prog,
        lastDate: p.assessment_updated_at || (p.has_assessment ? 'Saved' : 'Not started'),
        tag: null,
        provider: 'Dr. Maria Santos · Psychiatrist',
        patient: p,
      };
    });
    ASSESSMENTS = cards;
    buildAssessments(cards);
  }

  // Server-side search form handles filtering; keep local filter as no-op fallback.
  var assSearch = document.getElementById('assess-search');
  if (assSearch) {
    assSearch.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        if (this.form) this.form.submit();
      }
    });
  }

  document.addEventListener('click', function (e) {
    var openBtn = e.target.closest('[data-assess-open]');
    if (openBtn) {
      openAssessDetail(openBtn.getAttribute('data-assess-open'));
      return;
    }
    var card = e.target.closest('#assessment-cards-grid .assess-card');
    if (card && !e.target.closest('button')) {
      openAssessDetail(card.getAttribute('data-assess-id'));
    }
  });

  var CURRENT_ASSESSMENT = {};

  function escapeHtml(str) {
    return String(str || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function collectBiological() {
    return {
      bp: getVal('v-bp'),
      hr: getVal('v-hr'),
      temp: getVal('v-temp'),
      weight: getVal('v-weight'),
      height: getVal('v-height'),
      bmi: getVal('v-bmi'),
      cc: getVal('bio-cc'),
      hpi: getVal('bio-hpi'),
      pph: getVal('bio-pph'),
      pmh: getVal('bio-pmh'),
      fh: getVal('bio-fh'),
      sh: getVal('bio-sh'),
      ros: getVal('bio-ros'),
      pe: getVal('bio-pe'),
      lab: getVal('bio-lab'),
    };
  }

  function collectPsychological() {
    var si = 'none';
    qsa('input[name="psy-si"]').forEach(function (rb) { if (rb.checked) si = rb.value; });
    return {
      appearance: getVal('psy-appearance'),
      behavior: getVal('psy-behavior'),
      speech: getVal('psy-speech'),
      mood: getVal('psy-mood'),
      affect: getVal('psy-affect'),
      thought_process: getVal('psy-thought_process'),
      thought_content: getVal('psy-thought_content'),
      perception: getVal('psy-perception'),
      cognition: getVal('psy-cognition'),
      insight: getVal('psy-insight'),
      judgment: getVal('psy-judgment'),
      si: si,
      risk_notes: getVal('psy-risk_notes'),
      safety_plan: getVal('psy-safety_plan'),
    };
  }

  function collectSocial() {
    return {
      living: getVal('soc-living'),
      occupation: getVal('soc-occupation'),
      financial: getVal('soc-financial'),
      relationships: getVal('soc-relationships'),
      cultural: getVal('soc-cultural'),
      legal: getVal('soc-legal'),
      substance: getVal('soc-substance'),
      stressors: getVal('soc-stressors'),
    };
  }

  function collectSpiritual() {
    return {
      beliefs: getVal('spi-beliefs'),
      practices: getVal('spi-practices'),
      coping: getVal('spi-coping'),
      needs: getVal('spi-needs'),
      strengths: getVal('spi-strengths'),
      meaning: getVal('spi-meaning'),
    };
  }

  function collectPrayerPoints() {
    var points = [];
    qsa('#prayer-list .prayer-entry').forEach(function (el) {
      points.push({
        date: el.getAttribute('data-date') || '',
        author: el.getAttribute('data-author') || 'Dr. Maria Santos',
        text: el.getAttribute('data-text') || (el.querySelector('.prayer-text') ? el.querySelector('.prayer-text').textContent : ''),
      });
    });
    return points;
  }

  function collectIntervention() {
    var timeline = [];
    qsa('#intervention-timeline .timeline-entry').forEach(function (el) {
      timeline.push({
        date: el.getAttribute('data-date') || '',
        text: el.getAttribute('data-text') || (el.querySelector('.timeline-text') ? el.querySelector('.timeline-text').textContent : ''),
      });
    });
    return {
      plan: getVal('intervention-plan'),
      timeline: timeline,
      medical_history: {
        allergies: getVal('medhist-allergies'),
        medications: getVal('medhist-medications'),
        surgical: getVal('medhist-surgical'),
        hospitalizations: getVal('medhist-hospitalizations'),
        immunizations: getVal('medhist-immunizations'),
        family: getVal('medhist-family'),
      },
    };
  }

  function renderPrayerPoints(points) {
    var list = document.getElementById('prayer-list');
    if (!list) return;
    list.innerHTML = '';
    (points || []).forEach(function (point) {
      var entry = document.createElement('div');
      entry.className = 'prayer-entry';
      entry.setAttribute('data-date', point.date || '');
      entry.setAttribute('data-author', point.author || 'Dr. Maria Santos');
      entry.setAttribute('data-text', point.text || '');
      entry.innerHTML = '<div class="prayer-meta">' + escapeHtml(point.date || '') +
        ' · ' + escapeHtml(point.author || 'Dr. Maria Santos') +
        ' <button type="button" class="btn-outline-sm prayer-remove" style="margin-left:8px;">Remove</button></div>' +
        '<div class="prayer-text">' + escapeHtml(point.text || '') + '</div>';
      list.appendChild(entry);
    });
  }

  function renderInterventionTimeline(entries) {
    var list = document.getElementById('intervention-timeline');
    if (!list) return;
    list.innerHTML = '';
    (entries || []).forEach(function (item) {
      var entry = document.createElement('div');
      entry.className = 'timeline-entry';
      entry.setAttribute('data-date', item.date || '');
      entry.setAttribute('data-text', item.text || '');
      entry.innerHTML = '<div class="timeline-dot"></div>' +
        '<div class="timeline-content">' +
        '<div class="timeline-date">' + escapeHtml(item.date || '') +
        ' <button type="button" class="btn-outline-sm timeline-remove" style="margin-left:8px;">Remove</button></div>' +
        '<div class="timeline-text">' + escapeHtml(item.text || '') + '</div>' +
        '</div>';
      list.appendChild(entry);
    });
  }

  function hydrateAssessmentForm(assessment) {
    assessment = assessment || {};
    var bio = assessment.biological || {};
    setVal('v-bp', bio.bp); setVal('v-hr', bio.hr); setVal('v-temp', bio.temp);
    setVal('v-weight', bio.weight); setVal('v-height', bio.height); setVal('v-bmi', bio.bmi);
    setVal('bio-cc', bio.cc); setVal('bio-hpi', bio.hpi); setVal('bio-pph', bio.pph);
    setVal('bio-pmh', bio.pmh); setVal('bio-fh', bio.fh); setVal('bio-sh', bio.sh);
    setVal('bio-ros', bio.ros); setVal('bio-pe', bio.pe); setVal('bio-lab', bio.lab);

    var psy = assessment.psychological || {};
    ['appearance', 'behavior', 'speech', 'mood', 'affect', 'thought_process', 'thought_content', 'perception', 'cognition', 'insight', 'judgment', 'risk_notes', 'safety_plan'].forEach(function (k) {
      setVal('psy-' + k, psy[k]);
    });
    var si = psy.si || 'none';
    qsa('input[name="psy-si"]').forEach(function (rb) { rb.checked = rb.value === si; });

    var soc = assessment.social || {};
    ['living', 'occupation', 'financial', 'relationships', 'cultural', 'legal', 'substance', 'stressors'].forEach(function (k) {
      setVal('soc-' + k, soc[k]);
    });

    var spi = assessment.spiritual || {};
    ['beliefs', 'practices', 'coping', 'needs', 'strengths', 'meaning'].forEach(function (k) {
      setVal('spi-' + k, spi[k]);
    });

    renderPrayerPoints(assessment.prayer_points || []);

    var inter = assessment.intervention || {};
    setVal('intervention-plan', inter.plan || '');
    renderInterventionTimeline(inter.timeline || []);
    var mh = inter.medical_history || {};
    ['allergies', 'medications', 'surgical', 'hospitalizations', 'immunizations', 'family'].forEach(function (k) {
      setVal('medhist-' + k, mh[k]);
    });
  }

  function openAssessDetail(id) {
    var a = ASSESSMENTS.find(function (x) {
      return String(x.id) === String(id) || String(x.patient_id) === String(id);
    });
    if (!a) {
      // Deep-link may use patient_id before cards map exists; try API by numeric id if possible.
      var numericId = parseInt(id, 10);
      if (!numericId) return;
      a = { id: String(numericId), name: 'Patient', patient: { id: numericId } };
    }

    CURRENT_PATIENT = Object.assign({}, a.patient || {}, { id: a.patient && a.patient.id ? a.patient.id : a.id });
    hide(document.getElementById('assessment-list-view'));
    show(document.getElementById('assessment-detail-view'));
    document.getElementById('assess-detail-name').textContent = a.name || CURRENT_PATIENT.name || 'Patient';
    document.getElementById('assess-detail-id').textContent = a.patient_id || CURRENT_PATIENT.patient_id || a.id;
    hydrateAssessmentForm({});
    switchAssessTab('biological');

    var url = (ROUTES.assessmentsShow || '/psychiatrist/patients/__ID__/assessment').replace('__ID__', CURRENT_PATIENT.id);
    apiFetch(url).then(function (data) {
      CURRENT_ASSESSMENT = data.assessment || {};
      CURRENT_PATIENT = Object.assign({}, CURRENT_PATIENT, data.patient || {});
      document.getElementById('assess-detail-name').textContent = CURRENT_PATIENT.name || a.name;
      document.getElementById('assess-detail-id').textContent = CURRENT_PATIENT.patient_id || a.id;
      hydrateAssessmentForm(CURRENT_ASSESSMENT);
      ri();
    }).catch(function (err) {
      showToast(err.message || 'Unable to load assessment.');
    });
    ri();
  }

  var assessBackBtn = document.getElementById('assess-back-btn');
  if (assessBackBtn) assessBackBtn.addEventListener('click', function () {
    hide(document.getElementById('assessment-detail-view'));
    show(document.getElementById('assessment-list-view'));
    ri();
  });

  function switchAssessTab(tab) {
    qsa('#assess-tab-bar .assess-tab-btn').forEach(function (b) {
      b.classList.toggle('active', b.getAttribute('data-assess-tab') === tab);
    });
    qsa('#assessment-detail-view .tab-panel').forEach(function (p) {
      p.classList.toggle('active', p.getAttribute('data-assess-panel') === tab || p.id === ('tab-' + tab));
    });
  }

  qsa('#assess-tab-bar .assess-tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      switchAssessTab(this.getAttribute('data-assess-tab'));
    });
  });

  function saveAssessmentSection(section) {
    if (!CURRENT_PATIENT || !CURRENT_PATIENT.id) {
      showToast('Open a patient assessment first.');
      return;
    }

    var payload = {};
    if (section === 'biological') payload.biological = collectBiological();
    else if (section === 'psychological') payload.psychological = collectPsychological();
    else if (section === 'social') payload.social = collectSocial();
    else if (section === 'spiritual') payload.spiritual = collectSpiritual();
    else if (section === 'prayer_points') payload.prayer_points = collectPrayerPoints();
    else if (section === 'intervention') payload.intervention = collectIntervention();
    else {
      payload = {
        biological: collectBiological(),
        psychological: collectPsychological(),
        social: collectSocial(),
        spiritual: collectSpiritual(),
        prayer_points: collectPrayerPoints(),
        intervention: collectIntervention(),
      };
    }

    apiFetch((ROUTES.assessmentsStore || '').replace('__ID__', CURRENT_PATIENT.id), {
      method: 'POST',
      body: JSON.stringify(payload),
    }).then(function (data) {
      CURRENT_ASSESSMENT = data.assessment || Object.assign({}, CURRENT_ASSESSMENT, payload);
      CURRENT_PATIENT.assessment = CURRENT_ASSESSMENT;
      var card = ASSESSMENTS.find(function (x) { return String(x.id) === String(CURRENT_PATIENT.id); });
      if (card) {
        card.diag = (CURRENT_ASSESSMENT.biological && CURRENT_ASSESSMENT.biological.cc) || card.diag;
        card.lastDate = (data.updated_at || '').slice(0, 10) || card.lastDate;
        card.prog = Math.max(card.prog || 0, 20);
      }
      showToast(data.message || 'Assessment saved.');
    }).catch(function (err) {
      showToast(err.message || 'Unable to save assessment.');
    });
  }

  document.addEventListener('click', function (e) {
    var saveBtn = e.target.closest('[data-assess-save]');
    if (saveBtn && saveBtn.closest('#assessment-detail-view')) {
      e.preventDefault();
      saveAssessmentSection(saveBtn.getAttribute('data-assess-save'));
    }
    if (e.target.closest('.prayer-remove')) {
      var prayerEntry = e.target.closest('.prayer-entry');
      if (prayerEntry) prayerEntry.remove();
    }
    if (e.target.closest('.timeline-remove')) {
      var tlEntry = e.target.closest('.timeline-entry');
      if (tlEntry) tlEntry.remove();
    }
  });

  if (typeof populateAssessmentCards === 'function') populateAssessmentCards();

  /* ============================================================
     ACCORDION (Medical History)
  ============================================================ */
  function initAccordion() {
    qsa('#medhist-accordion .accordion-trigger').forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        var panel = this.nextElementSibling;
        var isOpen = panel.classList.contains('open');
        qsa('#medhist-accordion .accordion-trigger').forEach(function (t) {
          t.classList.remove('open');
          if (t.nextElementSibling) t.nextElementSibling.classList.remove('open');
        });
        if (!isOpen) {
          this.classList.add('open');
          panel.classList.add('open');
        }
        ri();
      });
    });

    var toggleAllBtn = document.getElementById('medhist-toggle-all');
    if (toggleAllBtn) {
      var allOpen = false;
      toggleAllBtn.addEventListener('click', function () {
        allOpen = !allOpen;
        qsa('#medhist-accordion .accordion-trigger').forEach(function (t) {
          if (allOpen) { t.classList.add('open'); if (t.nextElementSibling) t.nextElementSibling.classList.add('open'); }
          else { t.classList.remove('open'); if (t.nextElementSibling) t.nextElementSibling.classList.remove('open'); }
        });
        toggleAllBtn.textContent = allOpen ? 'Collapse All' : 'Expand All';
        ri();
      });
    }
  }

  /* ============================================================
     PRAYER POINTS
  ============================================================ */
  var addPrayerBtn = document.getElementById('add-prayer-btn');
  var prayerInput = document.getElementById('prayer-input');

  if (addPrayerBtn) {
    addPrayerBtn.addEventListener('click', function () {
      var text = prayerInput ? prayerInput.value.trim() : '';
      if (!text) { showToast('Please enter a prayer point.'); return; }
      var d = new Date();
      var dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      var points = collectPrayerPoints();
      points.unshift({ date: dateStr, author: 'Dr. Maria Santos', text: text });
      renderPrayerPoints(points);
      prayerInput.value = '';
      saveAssessmentSection('prayer_points');
    });
  }

  /* ============================================================
     INTERVENTION TIMELINE
  ============================================================ */
  var addInterventionBtn = document.getElementById('add-intervention-btn');
  var interventionInput = document.getElementById('intervention-input');

  if (addInterventionBtn) {
    addInterventionBtn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var text = interventionInput ? interventionInput.value.trim() : '';
      if (!text) { showToast('Please enter an intervention.'); return; }
      var d = new Date();
      var dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      var inter = collectIntervention();
      inter.timeline = inter.timeline || [];
      inter.timeline.unshift({ date: dateStr, text: text });
      setVal('intervention-plan', inter.plan || getVal('intervention-plan'));
      renderInterventionTimeline(inter.timeline);
      interventionInput.value = '';
      saveAssessmentSection('intervention');
    });
  }

