/* psychiatrist_lifestyle.js — Lifestyle monitoring cards */

  /* ============================================================
     BUILD LIFESTYLE
  ============================================================ */
  function metricColor(pct) {
    if (pct >= 70) return 'bar-green';
    if (pct >= 40) return 'bar-amber';
    return 'bar-red';
  }

  function clampPct(n) {
    n = Number(n) || 0;
    if (n < 0) return 0;
    if (n > 100) return 100;
    return Math.round(n);
  }

  function scoreFrequency(value, goodWords, badWords) {
    var v = String(value || '').toLowerCase();
    if (!v) return { pct: 40, label: '—' };
    for (var i = 0; i < goodWords.length; i++) {
      if (v.indexOf(goodWords[i]) >= 0) return { pct: 80, label: value };
    }
    for (var j = 0; j < badWords.length; j++) {
      if (v.indexOf(badWords[j]) >= 0) return { pct: 25, label: value };
    }
    return { pct: 55, label: value };
  }

  function phqStressPct(ls) {
    var map = {
      'Not at all': 15,
      'Several days': 45,
      'More than half the days': 70,
      'Nearly every day': 90,
    };
    var keys = ['phq_feeling_down', 'phq_feeling_tired', 'phq_trouble_sleeping', 'phq_little_interest'];
    var total = 0;
    var count = 0;
    keys.forEach(function (k) {
      if (ls[k] && map[ls[k]] != null) {
        total += map[ls[k]];
        count += 1;
      }
    });
    if (!count) return { pct: 40, label: 'Not assessed' };
    var pct = Math.round(total / count);
    var label = pct >= 70 ? 'High' : (pct >= 40 ? 'Moderate' : 'Low');
    return { pct: pct, label: label };
  }

  function lifestyleMetrics(ls) {
    ls = ls || {};
    var sleepHours = ls.sleep_hours != null && ls.sleep_hours !== '' ? Number(ls.sleep_hours) : null;
    var sleepPct = sleepHours == null ? 40 : clampPct((sleepHours / 8) * 100);
    var exercise = scoreFrequency(ls.exercise_frequency, ['daily', 'every day', '5', '6', '7', 'regular'], ['never', 'rare', 'none', 'sedentary', '0']);
    var nutrition = scoreFrequency(
      [ls.fruits_veg_servings, ls.fast_food_frequency, ls.weight_perception].filter(Boolean).join(' · ') || '',
      ['good', 'healthy', 'daily', '5', 'plenty'],
      ['poor', 'fair', 'often', 'daily fast', 'obese']
    );
    if (!ls.fruits_veg_servings && !ls.fast_food_frequency) {
      nutrition = { pct: 40, label: '—' };
    } else if (ls.fruits_veg_servings && !nutrition.label) {
      nutrition.label = ls.fruits_veg_servings;
    } else if (ls.fruits_veg_servings) {
      nutrition.label = ls.fruits_veg_servings;
    }
    var stress = phqStressPct(ls);
    var health = ls.health_score != null && ls.health_score !== ''
      ? { pct: clampPct(Number(ls.health_score) * 10), label: String(ls.health_score) + '/10' }
      : null;

    var metrics = [
      { label: 'Sleep', value: sleepHours == null ? '—' : (sleepHours + ' hrs'), pct: sleepPct, color: metricColor(sleepPct) },
      { label: 'Exercise', value: exercise.label || '—', pct: exercise.pct, color: metricColor(exercise.pct) },
      { label: 'Nutrition', value: nutrition.label || '—', pct: nutrition.pct, color: metricColor(nutrition.pct) },
      { label: 'Stress', value: stress.label, pct: stress.pct, color: metricColor(100 - stress.pct) },
    ];
    if (health) {
      metrics.unshift({ label: 'Health', value: health.label, pct: health.pct, color: metricColor(health.pct) });
    }
    return metrics;
  }

  function buildLifestyle(list) {
    var grid = document.getElementById('lifestyle-grid');
    var empty = document.getElementById('lifestyle-empty');
    if (!grid) return;
    list = Array.isArray(list) ? list : LIFESTYLE_PATIENTS;
    grid.innerHTML = '';

    if (!list.length) {
      if (empty) show(empty);
      return;
    }
    if (empty) hide(empty);

    list.forEach(function (p) {
      var metrics = lifestyleMetrics(p.lifestyle_assessment);
      var card = document.createElement('div');
      card.className = 'lifestyle-card';
      card.style.cursor = 'pointer';
      card.setAttribute('data-patient-id', p.id);

      var html = '<div class="lifestyle-card-top" style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:12px;">' +
        '<div>' +
        '<div class="lifestyle-card-name" style="margin-bottom:2px;">' + (p.name || 'Patient') + '</div>' +
        '<div style="font-size:12px;color:#64748b;">' + (p.patient_id || ('#' + p.id)) +
        (p.age != null ? ' · ' + p.age + 'y' : '') +
        (p.sex ? ' · ' + p.sex : '') + '</div>' +
        '</div>' +
        '<button type="button" class="btn-outline-sm lifestyle-open-btn" data-lifestyle-open="' + p.id + '">Open</button>' +
        '</div>';

      metrics.forEach(function (m) {
        html += '<div class="metric-row">' +
          '<span class="metric-label">' + m.label + '</span>' +
          '<div class="metric-track"><div class="metric-bar ' + m.color + '" style="width:' + m.pct + '%"></div></div>' +
          '<span class="metric-value">' + m.value + '</span>' +
          '</div>';
      });

      if (p.lifestyle_assessment && p.lifestyle_assessment.motivation_level) {
        html += '<div style="margin-top:8px;font-size:12px;color:#64748b;">Motivation: <strong style="color:#0f172a;">' +
          p.lifestyle_assessment.motivation_level + '</strong></div>';
      }

      card.innerHTML = html;
      card.addEventListener('click', function (e) {
        if (e.target.closest('.lifestyle-open-btn') || e.target === card || e.target.closest('.lifestyle-card')) {
          openLifestylePatient(p.id);
        }
      });
      grid.appendChild(card);
    });
    ri();
  }

  function openLifestylePatient(id) {
    var local = findPatientLocal(id);
    if (local && local.medical_history !== undefined) {
      populatePatientModal(local);
      switchPmTab('lifestyle');
    } else {
      var base = (window.PSYCH_ROUTES || {}).patientsShow || '/psychiatrist/patients';
      apiFetch(base + '/' + id)
        .then(function (data) {
          upsertPatientLocal(data.patient);
          populatePatientModal(data.patient);
          switchPmTab('lifestyle');
        })
        .catch(function (err) {
          showToast(err.message || 'Unable to load patient.');
        });
    }
  }

  function filterLifestyle() {
    var input = document.getElementById('lifestyle-search');
    var q = input ? input.value.toLowerCase().trim() : '';
    var list = LIFESTYLE_PATIENTS.filter(function (p) {
      if (!q) return true;
      return (p.name || '').toLowerCase().includes(q) || String(p.patient_id || '').toLowerCase().includes(q);
    });
    buildLifestyle(list);
  }

  var lifestyleSearch = document.getElementById('lifestyle-search');
  if (lifestyleSearch) lifestyleSearch.addEventListener('input', filterLifestyle);

