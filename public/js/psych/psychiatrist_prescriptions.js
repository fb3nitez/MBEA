/* psychiatrist_prescriptions.js — Rx/Dx subtabs, medication rows, preview, templates, print */

  /* ============================================================
     PRESCRIPTIONS — SUB-TABS
  ============================================================ */
  qsa('.subtab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var key = this.getAttribute('data-subtab');
      qsa('.subtab-btn').forEach(function (b) { b.classList.remove('active'); });
      qsa('.subtab-panel').forEach(function (p) { p.classList.remove('active'); });
      this.classList.add('active');
      var panel = document.getElementById('subtab-' + key);
      if (panel) panel.classList.add('active');
      ri();
    });
  });

  /* ============================================================
     RX: DATE
  ============================================================ */
  var rxDate = document.getElementById('rx-date');
  var dxDate = document.getElementById('dx-date');
  var todayStr = today();
  if (rxDate) rxDate.value = todayStr;
  if (dxDate) dxDate.value = todayStr;

  /* ============================================================
     RX: DIAGNOSIS TYPEAHEAD
  ============================================================ */
  var rxDiagInput = document.getElementById('rx-diagnosis');
  var rxDiagDropdown = document.getElementById('rx-diag-dropdown');

  if (rxDiagInput && rxDiagDropdown) {
    rxDiagInput.addEventListener('input', function () {
      var q = this.value.toLowerCase();
      if (!q) { hide(rxDiagDropdown); return; }
      var matches = DIAGNOSES.filter(function (d) { return d.toLowerCase().includes(q); });
      if (!matches.length) { hide(rxDiagDropdown); return; }
      rxDiagDropdown.innerHTML = '';
      matches.forEach(function (d) {
        var item = document.createElement('div');
        item.className = 'typeahead-item';
        item.textContent = d;
        item.addEventListener('click', function () {
          rxDiagInput.value = d;
          hide(rxDiagDropdown);
        });
        rxDiagDropdown.appendChild(item);
      });
      show(rxDiagDropdown);
    });

    document.addEventListener('click', function (e) {
      if (!rxDiagInput.contains(e.target) && !rxDiagDropdown.contains(e.target)) hide(rxDiagDropdown);
    });
  }

  /* ============================================================
     RX: MEDICATION ROWS
  ============================================================ */
  var rxMedsList = document.getElementById('rx-meds-list');
  var addMedBtn = document.getElementById('add-med-btn');

  function createMedRow(prefill) {
    prefill = prefill || {};
    var row = document.createElement('div');
    row.className = 'rx-med-row';

    // Med name
    var nameWrap = document.createElement('div');
    nameWrap.className = 'med-name-wrap';
    var nameInput = document.createElement('input');
    nameInput.type = 'text';
    nameInput.className = 'field-input';
    nameInput.placeholder = 'Medication name...';
    nameInput.value = prefill.name || '';
    var dropdown = document.createElement('div');
    dropdown.className = 'med-typeahead hidden';

    nameInput.addEventListener('input', function () {
      var q = this.value.toLowerCase();
      if (!q) { hide(dropdown); return; }
      var hits = MED_SUGGESTIONS.filter(function (m) { return m.toLowerCase().startsWith(q); });
      if (!hits.length) { hide(dropdown); return; }
      dropdown.innerHTML = '';
      hits.slice(0, 8).forEach(function (m) {
        var item = document.createElement('div');
        item.className = 'med-typeahead-item';
        item.textContent = m;
        item.addEventListener('click', function () {
          nameInput.value = m;
          hide(dropdown);
        });
        dropdown.appendChild(item);
      });
      show(dropdown);
    });

    document.addEventListener('click', function (e) {
      if (!nameWrap.contains(e.target)) hide(dropdown);
    });

    nameWrap.appendChild(nameInput);
    nameWrap.appendChild(dropdown);

    // Dosage
    var doseSelect = document.createElement('select');
    doseSelect.className = 'field-input';
    DOSAGES.forEach(function (d) {
      var opt = document.createElement('option');
      opt.value = d; opt.textContent = d;
      if (prefill.dose === d) opt.selected = true;
      doseSelect.appendChild(opt);
    });
    // Custom input
    var customDoseInput = document.createElement('input');
    customDoseInput.type = 'text';
    customDoseInput.className = 'field-input hidden';
    customDoseInput.placeholder = 'Custom dosage...';
    customDoseInput.style.marginTop = '4px';
    var doseWrap = document.createElement('div');
    doseWrap.appendChild(doseSelect);
    doseWrap.appendChild(customDoseInput);
    doseSelect.addEventListener('change', function () {
      if (this.value === 'Custom') show(customDoseInput);
      else hide(customDoseInput);
    });

    // Frequency
    var freqCol = document.createElement('div');
    freqCol.className = 'med-freq-col';
    var freqCheckboxes = document.createElement('div');
    freqCheckboxes.className = 'freq-checkboxes';
    var FREQS = ['Morning', 'Breakfast', 'Lunch', 'Dinner', 'Bedtime'];
    FREQS.forEach(function (f) {
      var lbl = document.createElement('label');
      lbl.className = 'freq-cb-label';
      var cb = document.createElement('input');
      cb.type = 'checkbox';
      if (prefill.freq && prefill.freq.includes(f)) cb.checked = true;
      lbl.appendChild(cb);
      lbl.appendChild(document.createTextNode(f));
      freqCheckboxes.appendChild(lbl);
    });
    var freqCustom = document.createElement('input');
    freqCustom.type = 'text';
    freqCustom.className = 'freq-custom-input';
    freqCustom.placeholder = 'Custom (e.g. PRN)...';
    freqCol.appendChild(freqCheckboxes);
    freqCol.appendChild(freqCustom);

    // Qty
    var qtyInput = document.createElement('input');
    qtyInput.type = 'number';
    qtyInput.className = 'med-qty-input';
    qtyInput.placeholder = 'Qty';
    qtyInput.value = prefill.qty || '';

    // Delete
    var delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.className = 'med-del-btn';
    delBtn.innerHTML = '<i data-feather="x"></i>';
    delBtn.addEventListener('click', function () {
      row.remove();
      ri();
    });

    row.appendChild(nameWrap);
    row.appendChild(doseWrap);
    row.appendChild(freqCol);
    row.appendChild(qtyInput);
    row.appendChild(delBtn);

    return row;
  }

  if (addMedBtn && rxMedsList) {
    addMedBtn.addEventListener('click', function () {
      rxMedsList.appendChild(createMedRow());
      ri();
    });
    // Add one row by default
    rxMedsList.appendChild(createMedRow());
    ri();
  }

  /* ============================================================
     RX: GENERATE Rx PREVIEW
  ============================================================ */
  var generateRxBtn = document.getElementById('generate-rx-btn');
  if (generateRxBtn) {
    generateRxBtn.addEventListener('click', function () {
      var patientId = document.getElementById('rx-patient').value;
      var patient = findPatientLocal(patientId);
      var patientLabel = patient ? (patient.name || '—') : '—';
      var age = (document.getElementById('rx-age').value) || '—';
      var date = (document.getElementById('rx-date').value) || todayStr;
      var diag = (document.getElementById('rx-diagnosis').value) || '—';
      var notes = (document.getElementById('rx-notes').value) || '—';

      document.getElementById('preview-patient').textContent = patientLabel;
      document.getElementById('preview-age').textContent = age;
      document.getElementById('preview-date').textContent = date;
      document.getElementById('preview-diag').textContent = diag;
      document.getElementById('preview-notes').textContent = notes;

      var medsList = document.getElementById('preview-meds-list');
      medsList.innerHTML = '';
      var medications = [];
      var rows = qsa('.rx-med-row', rxMedsList);
      rows.forEach(function (row) {
        var name = row.querySelector('input[type="text"]').value;
        var sel = row.querySelector('select');
        var dose = sel ? sel.value : '';
        if (dose === 'Custom') {
          var ci = row.querySelectorAll('input[type="text"]')[1];
          dose = ci ? ci.value : '';
        }
        var freq = [];
        row.querySelectorAll('input[type="checkbox"]:checked').forEach(function (cb) {
          freq.push(cb.parentElement.textContent.trim());
        });
        var customFreq = row.querySelector('.freq-custom-input');
        if (customFreq && customFreq.value.trim()) freq.push(customFreq.value.trim());
        var qty = row.querySelector('.med-qty-input').value;
        if (!name) return;
        var med = { name: name, dose: dose, frequency: freq.join(', '), qty: qty };
        medications.push(med);
        var li = document.createElement('li');
        li.textContent = name + ' ' + dose + (freq.length ? ' — ' + freq.join(', ') : '') + (qty ? ' · Qty: ' + qty : '');
        medsList.appendChild(li);
      });

      if (!patientId) {
        showToast('Please select a patient first.');
        return;
      }

      apiFetch((ROUTES.prescriptionsStore || '').replace('__ID__', patientId), {
        method: 'POST',
        body: JSON.stringify({ diagnosis: diag, medications: medications, notes: notes, status: 'Draft' }),
      }).then(function (data) {
        showToast(data.message || 'Prescription saved.');
      }).catch(function (err) {
        showToast(err.message || 'Unable to save prescription.');
      });

      ri();
    });
  }

  /* ============================================================
     RX / DX: TEMPLATE LIBRARY (DB-backed)
  ============================================================ */
  function normalizeTemplate(t) {
    var payload = t.payload || {};
    return {
      id: t.id,
      type: t.type,
      name: t.name,
      tag: t.tag || '',
      tagClass: t.tagClass || t.tag_class || 'tag-psychiatric',
      desc: t.desc || t.description || '',
      diag: t.diag || payload.diag || '',
      meds: t.meds || payload.meds || [],
      tests: t.tests || payload.tests || [],
      payload: payload,
    };
  }

  function upsertTemplateLocal(template) {
    var t = normalizeTemplate(template);
    var list = t.type === 'dx' ? DX_TEMPLATES : RX_TEMPLATES;
    var idx = list.findIndex(function (item) { return String(item.id) === String(t.id); });
    if (idx >= 0) list[idx] = t;
    else list.push(t);
    if (t.type === 'dx') DX_TEMPLATES = list;
    else RX_TEMPLATES = list;
  }

  function removeTemplateLocal(type, id) {
    if (type === 'dx') {
      DX_TEMPLATES = DX_TEMPLATES.filter(function (t) { return String(t.id) !== String(id); });
    } else {
      RX_TEMPLATES = RX_TEMPLATES.filter(function (t) { return String(t.id) !== String(id); });
    }
  }

  function buildTemplateItem(t, onApply) {
    var div = document.createElement('div');
    div.className = 'template-item';
    div.innerHTML = '<div class="template-item-top">' +
      '<span class="template-name">' + (t.name || 'Template') + '</span>' +
      (t.tag ? '<span class="template-tag ' + (t.tagClass || '') + '">' + t.tag + '</span>' : '') +
      '</div>' +
      '<div class="template-desc">' + (t.desc || '') + '</div>' +
      '<div class="template-actions" style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px;">' +
      '<button type="button" class="btn-outline-sm tpl-apply">+ Apply</button>' +
      '<button type="button" class="btn-outline-sm tpl-edit">Edit</button>' +
      '<button type="button" class="btn-outline-sm tpl-delete" style="color:#b91c1c;">Delete</button>' +
      '</div>';
    div.querySelector('.tpl-apply').addEventListener('click', function () { onApply(t); });
    div.querySelector('.tpl-edit').addEventListener('click', function () { openTemplateModal(t.type, t); });
    div.querySelector('.tpl-delete').addEventListener('click', function () { deleteTemplate(t); });
    return div;
  }

  function buildRxTemplates() {
    var list = document.getElementById('rx-template-list');
    if (!list) return;
    list.innerHTML = '';
    if (!RX_TEMPLATES.length) {
      list.innerHTML = '<div class="template-item" style="color:#64748b;">No Rx templates yet. Click Manage / Add.</div>';
      return;
    }
    RX_TEMPLATES.map(normalizeTemplate).forEach(function (t) {
      list.appendChild(buildTemplateItem(t, applyRxTemplate));
    });
  }

  function buildDxTemplates() {
    var list = document.getElementById('dx-template-list');
    if (!list) return;
    list.innerHTML = '';
    if (!DX_TEMPLATES.length) {
      list.innerHTML = '<div class="template-item" style="color:#64748b;">No diagnostic templates yet. Click Manage / Add.</div>';
      return;
    }
    DX_TEMPLATES.map(normalizeTemplate).forEach(function (t) {
      list.appendChild(buildTemplateItem(t, applyDxTemplate));
    });
  }

  function applyRxTemplate(t) {
    if (!rxMedsList) return;
    t = normalizeTemplate(t);
    rxMedsList.innerHTML = '';
    (t.meds || []).forEach(function (m) {
      rxMedsList.appendChild(createMedRow(m));
    });
    if (rxDiagInput) rxDiagInput.value = t.diag || '';
    ri();
    showToast('Template "' + t.name + '" applied.');
  }

  function applyDxTemplate(t) {
    t = normalizeTemplate(t);
    qsa('.dx-cb').forEach(function (cb) { cb.checked = false; });
    (t.tests || []).forEach(function (test) {
      var cb = qs('.dx-cb[data-test="' + test + '"]');
      if (cb) cb.checked = true;
    });
    showToast('Template "' + t.name + '" applied.');
  }

  function parseMedsText(text) {
    return String(text || '').split('\n').map(function (line) {
      line = line.trim();
      if (!line) return null;
      var parts = line.split('|').map(function (p) { return p.trim(); });
      return {
        name: parts[0] || '',
        dose: parts[1] || '',
        freq: parts[2] ? parts[2].split(',').map(function (f) { return f.trim(); }).filter(Boolean) : [],
        qty: parts[3] ? Number(parts[3]) || parts[3] : '',
      };
    }).filter(Boolean);
  }

  function medsToText(meds) {
    return (meds || []).map(function (m) {
      return [m.name || '', m.dose || '', Array.isArray(m.freq) ? m.freq.join(', ') : (m.freq || ''), m.qty || ''].join(' | ');
    }).join('\n');
  }

  function parseTestsText(text) {
    return String(text || '')
      .split(/[\n,]+/)
      .map(function (t) { return t.trim(); })
      .filter(Boolean);
  }

  function openTemplateModal(type, template) {
    setVal('tpl-id', template ? template.id : '');
    setVal('tpl-type', type);
    setVal('tpl-name', template ? template.name : '');
    setVal('tpl-tag', template ? template.tag : '');
    setVal('tpl-desc', template ? (template.desc || '') : '');
    setVal('tpl-diag', template ? (template.diag || '') : '');
    setVal('tpl-meds', template ? medsToText(template.meds) : '');
    setVal('tpl-tests', template ? (template.tests || []).join(', ') : '');

    var title = document.getElementById('template-modal-title');
    if (title) title.textContent = (template ? 'Edit' : 'Add') + (type === 'dx' ? ' Diagnostic Template' : ' Rx Template');

    var diagWrap = document.getElementById('tpl-diag-wrap');
    var medsWrap = document.getElementById('tpl-meds-wrap');
    var testsWrap = document.getElementById('tpl-tests-wrap');
    if (type === 'dx') {
      if (diagWrap) hide(diagWrap);
      if (medsWrap) hide(medsWrap);
      if (testsWrap) show(testsWrap);
    } else {
      if (diagWrap) show(diagWrap);
      if (medsWrap) show(medsWrap);
      if (testsWrap) hide(testsWrap);
    }
    openModal('template-modal');
  }

  function deleteTemplate(t) {
    if (!t.id) return;
    if (!window.confirm('Delete template "' + t.name + '"?')) return;
    var base = (window.PSYCH_ROUTES || {}).templatesUpdate || '/psychiatrist/clinical-templates';
    apiFetch(base + '/' + t.id, { method: 'DELETE' })
      .then(function (data) {
        removeTemplateLocal(t.type, t.id);
        buildRxTemplates();
        buildDxTemplates();
        showToast(data.message || 'Template deleted.');
      })
      .catch(function (err) { showToast(err.message); });
  }

  var rxTemplateAddBtn = document.getElementById('rx-template-add-btn');
  if (rxTemplateAddBtn) rxTemplateAddBtn.addEventListener('click', function () { openTemplateModal('rx'); });
  var dxTemplateAddBtn = document.getElementById('dx-template-add-btn');
  if (dxTemplateAddBtn) dxTemplateAddBtn.addEventListener('click', function () { openTemplateModal('dx'); });

  var tplSaveBtn = document.getElementById('tpl-save-btn');
  if (tplSaveBtn) {
    tplSaveBtn.addEventListener('click', function () {
      var id = getVal('tpl-id');
      var type = getVal('tpl-type') || 'rx';
      var name = getVal('tpl-name').trim();
      if (!name) { showToast('Template name is required.'); return; }

      var payload = {
        type: type,
        name: name,
        tag: getVal('tpl-tag').trim() || null,
        description: getVal('tpl-desc').trim() || null,
      };
      if (type === 'dx') {
        payload.tests = parseTestsText(getVal('tpl-tests'));
      } else {
        payload.diag = getVal('tpl-diag').trim() || null;
        payload.meds = parseMedsText(getVal('tpl-meds'));
      }

      var routes = window.PSYCH_ROUTES || {};
      var url = id
        ? ((routes.templatesUpdate || '/psychiatrist/clinical-templates') + '/' + id)
        : (routes.templatesStore || '/psychiatrist/clinical-templates');
      var method = id ? 'PUT' : 'POST';

      apiFetch(url, { method: method, body: JSON.stringify(payload) })
        .then(function (data) {
          upsertTemplateLocal(data.template);
          buildRxTemplates();
          buildDxTemplates();
          closeModal('template-modal');
          showToast(data.message || 'Template saved.');
        })
        .catch(function (err) { showToast(err.message); });
    });
  }

  /* ============================================================
     RX: PRINT
  ============================================================ */
  window.printRx = function () {
    var el = document.getElementById('print-area-rx');
    if (!el) return;
    var win = window.open('', '_blank', 'width=600,height=700');
    win.document.write('<html><head><title>MedCare Rx</title><style>body{font-family:sans-serif;padding:40px;font-size:13px;color:#0f172a;}.rx-preview-stamp{font-size:32px;font-weight:900;color:#2563eb;font-style:italic;}.rx-sig-line-bar{height:1px;background:#0f172a;width:140px;margin-bottom:4px;}</style></head><body>');
    win.document.write(el.innerHTML);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
  };

  /* ============================================================
     DIAGNOSTIC REQUEST
  ============================================================ */
  function buildDxChecklist() {
    var list = document.getElementById('dx-checklist');
    if (!list) return;
    list.innerHTML = '';
    DX_LAB_GROUPS.forEach(function (g) {
      var cat = document.createElement('div');
      cat.className = 'dx-cat-label';
      cat.textContent = g.cat;
      list.appendChild(cat);
      g.tests.forEach(function (test) {
        var lbl = document.createElement('label');
        lbl.className = 'dx-check-item';
        var cb = document.createElement('input');
        cb.type = 'checkbox';
        cb.className = 'dx-cb';
        cb.setAttribute('data-test', test);
        lbl.appendChild(cb);
        lbl.appendChild(document.createTextNode(test));
        list.appendChild(lbl);
      });
    });
  }

  var generateDxBtn = document.getElementById('generate-dx-btn');
  if (generateDxBtn) {
    generateDxBtn.addEventListener('click', function () {
      var patientId = document.getElementById('dx-patient').value;
      var patient = findPatientLocal(patientId);
      var patientLabelText = patient ? (patient.name || '—') : (document.getElementById('dx-patient-search').value || '—');
      var date = document.getElementById('dx-date').value || todayStr;
      var notes = document.getElementById('dx-notes').value || '—';

      document.getElementById('dx-prev-patient').textContent = patientLabelText;
      document.getElementById('dx-prev-date').textContent = date;
      document.getElementById('dx-prev-notes').textContent = notes;

      var testList = document.getElementById('dx-prev-tests');
      testList.innerHTML = '';
      qsa('.dx-cb:checked').forEach(function (cb) {
        var li = document.createElement('li');
        li.textContent = cb.getAttribute('data-test');
        testList.appendChild(li);
      });
      var otherImg = document.getElementById('dx-other-imaging');
      if (otherImg && otherImg.value.trim()) {
        var li2 = document.createElement('li');
        li2.textContent = otherImg.value.trim();
        testList.appendChild(li2);
      }

      if (!patientId) {
        showToast('Please select a patient first.');
        return;
      }
      showToast('Diagnostic request generated.');
    });
  }

  window.printDx = function () {
    var el = document.getElementById('print-area-dx');
    if (!el) return;
    var win = window.open('', '_blank', 'width=600,height=700');
    win.document.write('<html><head><title>MedCare Diagnostic Request</title><style>body{font-family:sans-serif;padding:40px;font-size:13px;color:#0f172a;}.rx-sig-line-bar{height:1px;background:#0f172a;width:140px;margin-bottom:4px;}</style></head><body>');
    win.document.write(el.innerHTML);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
  };

