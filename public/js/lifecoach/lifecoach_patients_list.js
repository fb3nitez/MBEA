/* lifecoach_patients.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('patients');

  var PATIENTS = (LC_DATA.PATIENTS || []).slice();
  var currentPatientId = null;
  var complianceChart = null;
  var DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

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
    if (tab === 'metrics') buildComplianceChart();
    lcRi();
  };

  document.querySelectorAll('.tab-btn[data-ptab]').forEach(function (btn) {
    btn.addEventListener('click', function () { switchPatientTab(this.getAttribute('data-ptab')); });
  });

  function syncGoalHabitData(p) {
    if (!p) return;

    var goals = p.goals || [];
    var priorRows = Array.isArray(p.habitData) ? p.habitData : [];

    p.habits = [];
    p.habitData = [];

    goals.forEach(function (goal, index) {
      var priorRow = Array.isArray(priorRows[index]) ? priorRows[index].slice() : [];
      var row = [];

      for (var i = 0; i < DAYS.length; i++) {
        row.push(Boolean(Array.isArray(priorRow) && priorRow.length === DAYS.length && priorRow[i] === true));
      }

      p.habits.push(goal.title || 'Goal');
      p.habitData.push(row);
    });
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
    buildHabits(p);
    buildPatientNotes(p);
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

  function buildComplianceChart() {
    var p = PATIENTS.find(function (x) { return String(x.id) === String(currentPatientId); });
    if (!p) return;
    var canvas = document.getElementById('compliance-chart');
    if (!canvas || typeof Chart === 'undefined') return;
    if (complianceChart) { complianceChart.destroy(); complianceChart = null; }
    complianceChart = new Chart(canvas, {
      type: 'line',
      data: {
        labels: DAYS,
        datasets: [{
          label: 'Compliance %',
          data: p.compliance || [0, 0, 0, 0, 0, 0, 0],
          borderColor: '#16a34a',
          backgroundColor: 'rgba(22,163,74,.08)',
          borderWidth: 2.5,
          pointBackgroundColor: '#16a34a',
          pointRadius: 4,
          tension: 0.4,
          fill: true,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { min: 0, max: 100, grid: { color: '#f1f5f9' }, ticks: { callback: function (v) { return v + '%'; }, font: { size: 11 } } },
          x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        },
      },
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
      var div = document.createElement('div');
      div.className = 'goal-item';
      div.innerHTML =
        '<div class="goal-cat-dot" style="background:' + color + ';"></div>' +
        '<div class="goal-body">' +
        '<div class="goal-top"><span class="goal-title-text">' + lcEscape(g.title) + '</span><span class="goal-cat-tag" style="background:' + color + '18;color:' + color + ';border:1px solid ' + color + '30;">' + lcEscape(g.cat) + '</span></div>' +
        '<div class="goal-desc">' + lcEscape(g.desc) + '</div>' +
        '<div class="goal-date">Target: ' + lcEscape(g.date) + '</div>' +
        '<div class="goal-progress-row"><div class="goal-prog-track"><div class="goal-prog-bar" style="width:' + g.prog + '%;background:linear-gradient(90deg,' + color + ',' + color + '88);"></div></div><span class="goal-prog-pct" style="color:' + color + ';">' + g.prog + '%</span></div>' +
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
            syncGoalHabitData(p);
            buildGoals(p);
            buildHabits(p);
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
          syncGoalHabitData(p);
          buildGoals(p);
          buildHabits(p);
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
            syncGoalHabitData(p);
            buildGoals(p);
            buildHabits(p);
            populateDetail(p);
          }
          lcToast(data.message || 'Goal deleted.');
        }).catch(function (err) {
          lcToast(err.message || 'Could not delete goal.');
        });
    }
  });

  function buildHabits(p) {
    var wrap = document.getElementById('pd-habits-wrap');
    if (!wrap) return;
    if (!(p.habits || []).length) {
      wrap.innerHTML = '<div style="padding:16px;color:#94a3b8;font-size:13px;">No weekly habit goals have been added yet.</div>';
      return;
    }
    var table = document.createElement('table');
    table.className = 'habits-table';
    var thead = '<thead><tr><th>Habit</th>';
    DAYS.forEach(function (d) { thead += '<th>' + d + '</th>'; });
    thead += '</tr></thead>';
    table.innerHTML = thead;
    var tbody = document.createElement('tbody');
    p.habits.forEach(function (habit, hi) {
      var tr = document.createElement('tr');
      var row = '<td>' + lcEscape(habit) + '</td>';
      DAYS.forEach(function (d, di) {
        var checked = p.habitData[hi] && p.habitData[hi][di];
        row += '<td><input type="checkbox" class="habit-check ' + (checked ? 'habit-done' : 'habit-miss') + '" ' + (checked ? 'checked' : '') + ' data-goal-id="' + (p.goals[hi] && p.goals[hi].id || '') + '" data-habit-index="' + hi + '" data-day-index="' + di + '" aria-label="Weekly habit check for ' + lcEscape(habit) + ' on ' + d + '"></td>';
      });
      tr.innerHTML = row;
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    wrap.innerHTML = '';
    wrap.appendChild(table);
  }

  var habitTable = document.getElementById('pd-habits-wrap');
  if (habitTable) {
    habitTable.addEventListener('change', function (e) {
      if (!e.target.classList || !e.target.classList.contains('habit-check')) return;
      var wrap = e.target.closest('#pd-habits-wrap');
      if (!wrap) return;

      var p = PATIENTS.find(function (x) { return String(x.id) === String(currentPatientId); });
      if (!p) return;

      var goalId = e.target.getAttribute('data-goal-id');
      var habitIndex = Number(e.target.getAttribute('data-habit-index'));
      var dayIndex = Number(e.target.getAttribute('data-day-index'));

      if (!Number.isInteger(habitIndex) || !Number.isInteger(dayIndex) || !goalId) return;

      var goal = (p.goals || []).find(function (g) { return String(g.id) === String(goalId); });
      if (!goal) return;

      if (!p.habitData[habitIndex]) {
        p.habitData[habitIndex] = new Array(DAYS.length).fill(false);
      }

      p.habitData[habitIndex][dayIndex] = e.target.checked;

      var checkedCount = p.habitData[habitIndex].filter(Boolean).length;
      var newProgress = Math.round((checkedCount / DAYS.length) * 100);
      goal.prog = newProgress;

      lcApi(lcRoute('goalsProgress', goalId), {
        method: 'PUT',
        body: JSON.stringify({
          progress: newProgress,
          weekly_checkins: p.habitData[habitIndex],
        }),
      }).then(function (data) {
        var liveGoal = data.goal || data;
        var idx = (p.goals || []).findIndex(function (g) { return String(g.id) === String(goalId); });
        if (idx >= 0) {
          p.goals[idx] = Object.assign({}, p.goals[idx], liveGoal, {
            prog: Number(liveGoal.prog || liveGoal.progress || newProgress),
            weekly_checkins: Array.isArray(liveGoal.weekly_checkins) ? liveGoal.weekly_checkins : p.habitData[habitIndex],
          });
          p.habitData[habitIndex] = Array.isArray(liveGoal.weekly_checkins)
            ? liveGoal.weekly_checkins.slice(0, DAYS.length)
            : p.habitData[habitIndex];

          buildGoals(p);
        }
        lcToast('Habit progress updated.');
      }).catch(function (err) {
        lcToast(err.message || 'Could not save habit progress.');
      });
    });
  }

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
