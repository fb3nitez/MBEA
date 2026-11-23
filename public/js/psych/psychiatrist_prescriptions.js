/* psychiatrist_prescriptions.js - Rx/Dx subtabs, medication rows, preview, templates, print */

/* ============================================================
    PRESCRIPTIONS - SUB-TABS
  ============================================================ */
qsa(".subtab-btn").forEach(function (btn) {
    btn.addEventListener("click", function () {
        var key = this.getAttribute("data-subtab");
        qsa(".subtab-btn").forEach(function (b) {
            b.classList.remove("active");
        });
        qsa(".subtab-panel").forEach(function (p) {
            p.classList.remove("active");
        });
        this.classList.add("active");
        var panel = document.getElementById("subtab-" + key);
        if (panel) panel.classList.add("active");
        if (window.history && window.history.replaceState) {
            var hash = key === "diagnostic" ? "dx" : key === "lifestyle" ? "lifestyle" : "rx";
            window.history.replaceState(null, "", "#" + hash);
        }
        ri();
    });
});

/* Restore the selected prescription tab after a refresh. */
var initialHash = window.location.hash.toLowerCase();
var initialTab = initialHash === '#dx'
    ? 'diagnostic'
    : initialHash === '#lifestyle'
      ? 'lifestyle'
      : 'rx';
var initialTabButton = qs('.subtab-btn[data-subtab="' + initialTab + '"]');
if (initialTabButton) initialTabButton.click();

/* ============================================================
     RX: DATE
  ============================================================ */
var rxDate = document.getElementById("rx-date");
var dxDate = document.getElementById("dx-date");
var todayStr = today();
var todayInputValue = new Date().toISOString().slice(0, 10);
if (rxDate) rxDate.value = todayInputValue;
if (dxDate) dxDate.value = todayInputValue;

/* ============================================================
     RX: DIAGNOSIS TYPEAHEAD
  ============================================================ */
var rxDiagInput = document.getElementById("rx-diagnosis");
var rxDiagDropdown = document.getElementById("rx-diag-dropdown");

if (rxDiagInput && rxDiagDropdown) {
    rxDiagInput.addEventListener("input", function () {
        var q = this.value.toLowerCase();
        if (!q) {
            hide(rxDiagDropdown);
            return;
        }
        var matches = DIAGNOSES.filter(function (d) {
            return d.toLowerCase().includes(q);
        });
        if (!matches.length) {
            hide(rxDiagDropdown);
            return;
        }
        rxDiagDropdown.innerHTML = "";
        matches.forEach(function (d) {
            var item = document.createElement("div");
            item.className = "typeahead-item";
            item.textContent = d;
            item.addEventListener("click", function () {
                rxDiagInput.value = d;
                hide(rxDiagDropdown);
            });
            rxDiagDropdown.appendChild(item);
        });
        show(rxDiagDropdown);
    });

    document.addEventListener("click", function (e) {
        if (
            !rxDiagInput.contains(e.target) &&
            !rxDiagDropdown.contains(e.target)
        )
            hide(rxDiagDropdown);
    });
}

/* ============================================================
     RX: MEDICATION ROWS
  ============================================================ */
var rxMedsList = document.getElementById("rx-meds-list");
var addMedBtn = document.getElementById("add-med-btn");
var medicationSuggestionCounter = 0;

function attachMedicationSuggestions(input, wrap, dropdown, options) {
    var matches = [];
    var activeIndex = -1;
    var listId = "med-suggestions-" + (++medicationSuggestionCounter);
    dropdown.id = listId;
    dropdown.setAttribute("role", "listbox");
    input.setAttribute("role", "combobox");
    input.setAttribute("aria-autocomplete", "list");
    input.setAttribute("aria-controls", listId);
    input.setAttribute("aria-expanded", "false");

    function setOpen(open) {
        input.setAttribute("aria-expanded", open ? "true" : "false");
        if (open) show(dropdown);
        else hide(dropdown);
    }

    function selectOption(value) {
        input.value = value;
        setOpen(false);
        updateRxPreview();
    }

    function renderSuggestions(showAll) {
        var term = input.value.trim().toLowerCase();
        matches = options.filter(function (option) {
            return showAll || !term || option.toLowerCase().indexOf(term) >= 0;
        });
        activeIndex = -1;
        dropdown.innerHTML = "";
        if (!matches.length) {
            setOpen(false);
            return;
        }
        matches.forEach(function (option, index) {
            var item = document.createElement("div");
            item.className = "med-typeahead-item";
            item.setAttribute("role", "option");
            item.setAttribute("aria-selected", "false");
            item.textContent = option;
            item.addEventListener("mousedown", function (event) {
                event.preventDefault();
            });
            item.addEventListener("click", function () {
                selectOption(option);
            });
            dropdown.appendChild(item);
        });
        setOpen(true);
    }

    function setActiveOption(nextIndex) {
        var items = dropdown.querySelectorAll(".med-typeahead-item");
        if (!items.length) return;
        activeIndex = (nextIndex + items.length) % items.length;
        items.forEach(function (item, index) {
            item.classList.toggle("active", index === activeIndex);
            item.setAttribute("aria-selected", index === activeIndex ? "true" : "false");
        });
    }

    input.addEventListener("focus", function () { renderSuggestions(true); });
    input.addEventListener("click", function () { renderSuggestions(true); });
    input.addEventListener("input", function () { renderSuggestions(false); });
    input.addEventListener("keydown", function (event) {
        if (event.key === "ArrowDown" && !dropdown.classList.contains("hidden")) {
            event.preventDefault();
            setActiveOption(activeIndex + 1);
        } else if (event.key === "ArrowUp" && !dropdown.classList.contains("hidden")) {
            event.preventDefault();
            setActiveOption(activeIndex - 1);
        } else if (event.key === "Enter" && activeIndex >= 0) {
            event.preventDefault();
            selectOption(matches[activeIndex]);
        } else if (event.key === "Escape") {
            setOpen(false);
        }
    });
    document.addEventListener("click", function (event) {
        if (!wrap.contains(event.target)) setOpen(false);
    });
}

function createMedRow(prefill, templateId) {
    prefill = prefill || {};
    var row = document.createElement("div");
    row.className = "rx-med-row";
    if (templateId) row.setAttribute("data-template-id", templateId);

    // Med name
    var nameWrap = document.createElement("div");
    nameWrap.className = "med-name-wrap";
    var nameInput = document.createElement("input");
    nameInput.type = "text";
    nameInput.className = "field-input";
    nameInput.placeholder = "Medication name...";
    nameInput.value = prefill.name || "";
    var dropdown = document.createElement("div");
    dropdown.className = "med-typeahead hidden";

    nameInput.addEventListener("input", function () {
        var q = this.value.toLowerCase();
        if (!q) {
            hide(dropdown);
            return;
        }
        var hits = MED_SUGGESTIONS.filter(function (m) {
            return m.toLowerCase().startsWith(q);
        });
        if (!hits.length) {
            hide(dropdown);
            return;
        }
        dropdown.innerHTML = "";
        hits.slice(0, 8).forEach(function (m) {
            var item = document.createElement("div");
            item.className = "med-typeahead-item";
            item.textContent = m;
            item.addEventListener("click", function () {
                nameInput.value = m;
                hide(dropdown);
            });
            dropdown.appendChild(item);
        });
        show(dropdown);
    });

    document.addEventListener("click", function (e) {
        if (!nameWrap.contains(e.target)) hide(dropdown);
    });

    nameWrap.appendChild(nameInput);
    nameWrap.appendChild(dropdown);

    var doseWrap = document.createElement("div");
    doseWrap.className = "dose-wrap med-suggestion-wrap";
    var doseInput = document.createElement("input");
    doseInput.type = "text";
    doseInput.className = "field-input med-dose-input";
    doseInput.placeholder = "e.g. 50mg";
    doseInput.maxLength = 100;
    doseInput.value = prefill.dose || "";
    var doseDropdown = document.createElement("div");
    doseDropdown.className = "med-typeahead hidden";
    attachMedicationSuggestions(
        doseInput,
        doseWrap,
        doseDropdown,
        DOSAGES.filter(function (dosage) { return dosage !== "Custom"; }),
    );
    doseWrap.appendChild(doseInput);
    doseWrap.appendChild(doseDropdown);

    var freqCol = document.createElement("div");
    freqCol.className = "med-freq-col";
    var freqCheckboxes = document.createElement("div");
    freqCheckboxes.className = "freq-checkboxes";
    var frequencyOptions = ["Morning", "Breakfast", "Lunch", "Dinner", "Bedtime"];
    var prefillFrequencies = Array.isArray(prefill.freq)
        ? prefill.freq
        : String(prefill.freq || prefill.frequency || "")
              .split(",")
              .map(function (value) { return value.trim(); })
              .filter(Boolean);
    var customFrequencies = prefillFrequencies.filter(function (value) {
        return !frequencyOptions.some(function (frequency) {
            return String(value).toLowerCase() === frequency.toLowerCase();
        });
    });
    frequencyOptions.forEach(function (frequency) {
        var label = document.createElement("label");
        label.className = "freq-cb-label";
        var checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.checked = prefillFrequencies.some(function (value) {
            return String(value).toLowerCase() === frequency.toLowerCase();
        });
        label.appendChild(checkbox);
        label.appendChild(document.createTextNode(frequency));
        freqCheckboxes.appendChild(label);
    });
    var freqCustom = document.createElement("input");
    freqCustom.type = "text";
    freqCustom.className = "freq-custom-input";
    freqCustom.placeholder = "PRN / other timing";
    freqCustom.maxLength = 100;
    freqCustom.value = customFrequencies.join(", ");
    freqCol.appendChild(freqCheckboxes);
    freqCol.appendChild(freqCustom);

    // Qty
    var qtyInput = document.createElement("input");
    qtyInput.type = "number";
    qtyInput.min = "1";
    qtyInput.step = "1";
    qtyInput.className = "med-qty-input";
    qtyInput.placeholder = "Qty";
    qtyInput.value = prefill.qty || "";
    qtyInput.addEventListener("input", function () { validateMedicationRow(row); });

    // Delete
    var delBtn = document.createElement("button");
    delBtn.type = "button";
    delBtn.className = "med-del-btn";
    delBtn.innerHTML = '<i data-feather="trash-2"></i>';
    delBtn.addEventListener("click", function () {
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

function validateMedicationRow(row) {
    var qty = row.querySelector(".med-qty-input");
    var valid = true;
    if (qty && qty.value && (!/^\d+$/.test(qty.value) || Number(qty.value) < 1)) {
        qty.classList.add("field-invalid");
        valid = false;
    } else if (qty) {
        qty.classList.remove("field-invalid");
    }
    return valid;
}

if (addMedBtn && rxMedsList) {
    addMedBtn.addEventListener("click", function () {
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
var generateRxBtn = document.getElementById("generate-rx-btn");
if (generateRxBtn) {
    generateRxBtn.addEventListener("click", function () {
        var patientId = document.getElementById("rx-patient").value;
        var patient = findPatientLocal(patientId);
        var patientLabel = patient ? patient.name || "\u2014" : "\u2014";
        var age = document.getElementById("rx-age").value || "\u2014";
        var date = document.getElementById("rx-date").value || todayStr;
        var diag = document.getElementById("rx-diagnosis").value || "\u2014";
        var notes = document.getElementById("rx-notes").value || "\u2014";

        document.getElementById("preview-patient").textContent = patientLabel;
        document.getElementById("preview-age").textContent = age;
        document.getElementById("preview-date").textContent = date;
        document.getElementById("preview-diag").textContent = diag;
        document.getElementById("preview-notes").textContent = notes;

        var medsList = document.getElementById("preview-meds-list");
        medsList.innerHTML = "";
        var medications = [];
        var rows = qsa(".rx-med-row", rxMedsList);
        var invalidMedication = false;
        rows.forEach(function (row) {
            if (!validateMedicationRow(row)) invalidMedication = true;
            var name = row.querySelector(".med-name-wrap input").value.trim();
            var dose = row.querySelector(".med-dose-input").value.trim();
            var frequencies = [];
            row.querySelectorAll(".freq-checkboxes input:checked").forEach(function (checkbox) {
                frequencies.push(checkbox.parentElement.textContent.trim());
            });
            var customFrequency = row.querySelector(".freq-custom-input").value.trim();
            if (customFrequency) frequencies.push(customFrequency);
            var frequency = frequencies.join(", ");
            var qty = row.querySelector(".med-qty-input").value;
            if (!name) return;
            var med = {
                name: name,
                dose: dose,
                frequency: frequency,
                qty: qty,
            };
            medications.push(med);
            var li = document.createElement("li");
            li.textContent =
                name +
                (dose ? " " + dose : "") +
                (frequency ? " \u2014 " + frequency : "") +
                (qty ? " \u00b7 Qty: " + qty : "");
            medsList.appendChild(li);
        });

        if (invalidMedication) {
            showToast("Please correct dosage and quantity fields.");
            return;
        }

        if (!patientId) {
            showToast("Please select a patient first.");
            return;
        }

        var interventions = collectLxItems();
        if (!medications.length && !interventions.length) {
            showToast("Please add at least one medication or lifestyle intervention.");
            return;
        }
        var lxLabel = document.getElementById("preview-lifestyle-label");
        var lxList = document.getElementById("preview-lifestyle-list");
        if (lxList) {
            lxList.innerHTML = interventions.length
                ? buildLxGroupedHtml(interventions)
                : "";
        }
        if (lxLabel) {
            if (interventions.length) show(lxLabel);
            else hide(lxLabel);
        }

        apiFetch(
            (ROUTES.prescriptionsStore || "").replace("__ID__", patientId),
            {
                method: "POST",
                body: JSON.stringify({
                    diagnosis: diag,
                    medications: medications,
                    lifestyle_interventions: interventions,
                    notes: notes,
                    status: "Draft",
                }),
            },
        )
            .then(function (data) {
                showToast(data.message || "Prescription saved.");
            })
            .catch(function (err) {
                showToast(err.message || "Unable to save prescription.");
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
        tag: t.tag || "",
        tagClass: t.tagClass || t.tag_class || "tag-psychiatric",
        desc: t.desc || t.description || "",
        diag: t.diag || payload.diag || "",
        meds: t.meds || payload.meds || [],
        tests: t.tests || payload.tests || [],
        lifestyle: t.lifestyle || payload.lifestyle || [],
        favorite: !!t.favorite,
        lastUsedAt: t.last_used_at || t.lastUsedAt || null,
        usageCount: Number(t.usage_count || t.usageCount || 0),
        payload: payload,
    };
}

function upsertTemplateLocal(template) {
    var t = normalizeTemplate(template);
    var list = t.type === "dx" ? DX_TEMPLATES : RX_TEMPLATES;
    var idx = list.findIndex(function (item) {
        return String(item.id) === String(t.id);
    });
    if (idx >= 0) list[idx] = t;
    else list.push(t);
    if (t.type === "dx") DX_TEMPLATES = list;
    else RX_TEMPLATES = list;

    var manageTemplates = (window.PSYCH_DATA || {}).manageTemplates || [];
    var manageIndex = manageTemplates.findIndex(function (item) {
        return String(item.id) === String(template.id);
    });
    if (manageIndex >= 0) manageTemplates[manageIndex] = template;
    else manageTemplates.push(template);
    window.PSYCH_DATA.manageTemplates = manageTemplates;
}

function removeTemplateLocal(type, id) {
    if (type === "dx") {
        DX_TEMPLATES = DX_TEMPLATES.filter(function (t) {
            return String(t.id) !== String(id);
        });
    } else {
        RX_TEMPLATES = RX_TEMPLATES.filter(function (t) {
            return String(t.id) !== String(id);
        });
    }
}

var TEMPLATE_LIBRARY_STATE = {
    rx: { query: "", category: "All", favoritesOnly: false },
    dx: { query: "", category: "All", favoritesOnly: false },
};
var APPLIED_TEMPLATE_IDS = { rx: {}, dx: {} };

function templateFavoriteKey(type, id) {
    return "mbea-template-" + type + "-favorite-" + id;
}

function isTemplateFavorite(type, id) {
    try {
        return window.localStorage.getItem(templateFavoriteKey(type, id)) === "1";
    } catch (e) {
        return false;
    }
}

function setTemplateFavorite(type, id, favorite) {
    try {
        if (favorite) window.localStorage.setItem(templateFavoriteKey(type, id), "1");
        else window.localStorage.removeItem(templateFavoriteKey(type, id));
    } catch (e) {
        // Favorites remain optional when local storage is unavailable.
    }
    var route = (window.PSYCH_ROUTES || {}).templatesFavorite;
    if (route) apiFetch(route.replace('__ID__', id), { method: 'PUT', body: JSON.stringify({ favorite: favorite }) }).catch(function () {});
}

function templateSearchText(t) {
    return [
        t.name,
        t.tag,
        t.desc,
        (t.meds || []).map(function (m) { return [m.name, m.dose, m.freq, m.frequency].join(" "); }).join(" "),
        (t.tests || []).join(" "),
        (t.lifestyle || []).map(function (item) { return [item.title, item.category, item.target].join(" "); }).join(" "),
    ].join(" ").toLowerCase();
}

function templateCategories(type) {
    var list = type === "dx" ? DX_TEMPLATES : RX_TEMPLATES;
    var categories = [];
    list.map(normalizeTemplate).forEach(function (t) {
        var category = t.tag || (type === "dx" ? "Diagnostic" : "General");
        if (categories.indexOf(category) === -1) categories.push(category);
    });
    return categories.sort();
}

function templateMatches(t) {
    var state = TEMPLATE_LIBRARY_STATE[t.type];
    var category = t.tag || (t.type === "dx" ? "Diagnostic" : "General");
    return (!state.query || templateSearchText(t).indexOf(state.query.toLowerCase()) >= 0) &&
        (state.category === "All" || category === state.category) &&
        (!state.favoritesOnly || isTemplateFavorite(t.type, t.id));
}

function showTemplateChoiceModal(templateName) {
    return new Promise(function (resolve) {
        var overlay = document.createElement("div");
        overlay.className = "template-choice-overlay template-choice-enter";
        overlay.setAttribute("role", "dialog");
        overlay.setAttribute("aria-modal", "true");
        overlay.setAttribute("aria-labelledby", "template-choice-title");
        overlay.innerHTML =
            '<div class="template-choice-modal">' +
            '<div class="template-choice-header"><span class="template-choice-icon"><i data-feather="layers"></i></span><div><h3 id="template-choice-title">Apply template?</h3><span class="template-choice-subtitle">Choose how to update this prescription</span></div></div>' +
            '<p>This form already has data. Choose how to apply <strong>' + escHtml(templateName) + '</strong>.</p>' +
            '<div class="template-choice-actions">' +
            '<button type="button" class="template-choice-replace">Replace</button>' +
            '<button type="button" class="template-choice-merge">Merge</button>' +
            '<button type="button" class="template-choice-cancel">Cancel</button>' +
            '</div>' +
            '</div>';
        document.body.appendChild(overlay);
        ri();
        var closed = false;
        var close = function (result) {
            if (closed) return;
            closed = true;
            overlay.classList.remove("template-choice-enter");
            overlay.classList.add("template-choice-closing");
            setTimeout(function () {
                overlay.remove();
                resolve(result);
            }, 160);
        };
        overlay.querySelector(".template-choice-replace").addEventListener("click", function () { close("replace"); });
        overlay.querySelector(".template-choice-merge").addEventListener("click", function () { close("merge"); });
        overlay.querySelector(".template-choice-cancel").addEventListener("click", function () { close("cancel"); });
        overlay.addEventListener("click", function (event) {
            if (event.target === overlay) close("cancel");
        });
        overlay.addEventListener("keydown", function (event) {
            if (event.key === "Escape") close("cancel");
        });
        overlay.querySelector(".template-choice-replace").focus();
    });
}

function applyTemplateWithChoice(t, onApply) {
    var hasData = t.type === "rx"
        ? collectRxMeds().length || collectLxItems().length || getVal("rx-diagnosis").trim()
        : qsa(".dx-cb:checked").length || getVal("dx-other-imaging").trim() || getVal("dx-notes").trim();
    var choice = hasData ? showTemplateChoiceModal(t.name) : Promise.resolve("replace");
    choice.then(function (mode) {
        if (mode === "cancel") return;
        if (mode === "replace") APPLIED_TEMPLATE_IDS[t.type] = {};
        onApply(t, mode);
        APPLIED_TEMPLATE_IDS[t.type][String(t.id)] = true;
        var usedRoute = (window.PSYCH_ROUTES || {}).templatesUsed;
        if (usedRoute) apiFetch(usedRoute.replace('__ID__', t.id), { method: 'POST' }).catch(function () {});
        buildTemplateLibraries();
    });
}

function buildTemplateItem(t, onApply) {
    var div = document.createElement("div");
    div.className = "template-item";
    div.setAttribute("role", "listitem");
    var favorite = t.favorite || isTemplateFavorite(t.type, t.id);
    var applied = !!APPLIED_TEMPLATE_IDS[t.type][String(t.id)];
    var entries = t.type === "dx"
        ? (t.tests || []).map(function (test) { return "<li>" + escHtml(test) + "</li>"; }).join("")
        : (t.meds || []).map(function (m) {
            var freq = Array.isArray(m.freq) ? m.freq.join(", ") : (m.frequency || m.freq || "");
            return "<li><strong>" + escHtml(m.name || "Medication") + "</strong> " + escHtml(m.dose || "") + (freq ? " &middot; " + escHtml(freq) : "") + "</li>";
        }).concat((t.lifestyle || []).map(function (item) {
            return "<li><strong>" + escHtml(item.title || "Intervention") + "</strong> <span class=\"template-detail-muted\">" + escHtml(item.category || "") + "</span></li>";
        })).join("");
    var summary = t.desc || (t.type === "dx" ? (t.tests || []).slice(0, 3).join(", ") : (t.meds || []).map(function (m) { return m.name || ""; }).concat((t.lifestyle || []).map(function (item) { return item.title || ""; })).filter(Boolean).slice(0, 3).join(", "));
    var meta = t.type === "dx" ? (t.tests || []).length + " tests" : (t.meds || []).length + " medications · " + (t.lifestyle || []).length + " lifestyle items";
    var templateName = String(t.name || "Template");
    var displayName = escHtml(templateName).replace(/\s+—\s+/g, "&nbsp;— ");
    var applyButton = applied
        ? '<button type="button" class="template-apply-target template-applied-button" aria-pressed="true" aria-label="Active in the form. Click to remove" title="Active in the form. Click to remove"><i data-feather="check"></i><span class="sr-only">Active in the form</span></button>'
        : '<button type="button" class="template-apply-target" aria-label="Apply ' + escHtml(t.name || "Template") + '" title="Apply template"><i data-feather="play"></i><span class="sr-only">Apply</span></button>';
    div.innerHTML =
                '<div class="template-item-top">' + applyButton +
                '<div class="template-item-content"><strong class="template-name" title="' + escHtml(templateName) + '">' + displayName + '</strong>' +
                '<div class="template-item-meta">' + (t.tag ? '<span class="template-tag ' + (t.tagClass || "") + '">' + escHtml(t.tag) + '</span>' : '') + '<span class="template-meta">' + escHtml(meta) + '</span></div>' +
                '<div class="template-desc">' + escHtml(summary || "No details provided") + '</div></div>' +
                '<div class="template-item-actions"><button type="button" class="template-favorite" aria-label="' + (favorite ? "Remove from favorites" : "Add to favorites") + ' ' + escHtml(templateName) + '" title="Favorites" aria-pressed="' + (favorite ? "true" : "false") + '"><i data-feather="star"></i></button>' +
                '<button type="button" class="template-preview-toggle" aria-expanded="false" aria-label="Preview ' + escHtml(templateName) + '" title="Preview"><i data-feather="chevron-down"></i></button></div>' +
                '</div><details class="template-preview"><summary class="sr-only">Preview details</summary><ul>' + (entries || "<li>No details provided</li>") + "</ul></details>" +
        "";
    if (!applied) {
        div.querySelector(".template-apply-target").addEventListener("click", function () {
            applyTemplateWithChoice(t, onApply);
        });
    } else {
        div.querySelector(".template-apply-target").addEventListener("click", function () {
            removeTemplateContribution(t);
        });
    }
    div.querySelector(".template-favorite").addEventListener("click", function () {
        var next = !isTemplateFavorite(t.type, t.id);
        setTemplateFavorite(t.type, t.id, next);
        buildTemplateLibraries();
    });
    div.querySelector(".template-preview-toggle").addEventListener("click", function (event) {
        event.stopPropagation();
        var toggle = this;
        var current = document.querySelector('.template-detail-popover');
        document.querySelectorAll('.template-preview-toggle[aria-expanded="true"]').forEach(function (button) { button.setAttribute('aria-expanded', 'false'); });
        if (current) current.remove();
        var open = toggle.getAttribute('aria-expanded') !== 'true';
        if (!open) { toggle.setAttribute('aria-expanded', 'false'); return; }
        var popover = document.createElement('div');
        popover.className = 'template-detail-popover';
        popover.setAttribute('role', 'dialog');
        popover.innerHTML = '<strong>Details</strong><ul>' + (entries || '<li>No details provided</li>') + '</ul>';
        document.body.appendChild(popover);
        var rect = this.getBoundingClientRect();
        var left = Math.min(rect.left, window.innerWidth - popover.offsetWidth - 12);
        var top = rect.bottom + 8;
        if (top + popover.offsetHeight > window.innerHeight - 12) top = Math.max(12, rect.top - popover.offsetHeight - 8);
        popover.style.left = Math.max(12, left) + 'px';
        popover.style.top = top + 'px';
        toggle.setAttribute('aria-expanded', 'true');
        function closePopover() { popover.remove(); toggle.setAttribute('aria-expanded', 'false'); document.removeEventListener('click', outside); document.removeEventListener('keydown', escape); }
        function outside(click) { if (!popover.contains(click.target) && click.target !== toggle) closePopover(); }
        function escape(keyEvent) { if (keyEvent.key === 'Escape') closePopover(); }
        document.addEventListener('click', outside);
        document.addEventListener('keydown', escape);
    });
    return div;
}

function removeTemplateContribution(t) {
    var id = String(t.id);
    if (t.type === 'rx') {
        qsa('.rx-med-row[data-template-id="' + id + '"]:not([data-template-edited="true"])').forEach(function (row) { row.remove(); });
        qsa('.lx-item-row[data-template-id="' + id + '"]:not([data-template-edited="true"])').forEach(function (row) { row.remove(); });
        APPLIED_TEMPLATE_IDS.rx[id] = false;
        updateRxPreview();
    } else {
        qsa('.dx-cb[data-template-id="' + id + '"]:not([data-template-edited="true"])').forEach(function (cb) { cb.checked = false; cb.removeAttribute('data-template-id'); });
        APPLIED_TEMPLATE_IDS.dx[id] = false;
        updateDxSelectedCount();
        updateDxPreview();
    }
    buildTemplateLibraries();
    showToast('Template removed; manually edited items were kept.');
}

function buildTemplateLibrary(type) {
    var list = document.getElementById(type + "-template-list");
    var filters = document.getElementById(type + "-template-filters");
    if (!list) return;
    var allTemplates = (type === "dx" ? DX_TEMPLATES : RX_TEMPLATES).map(normalizeTemplate);
    var templates = allTemplates.filter(templateMatches);
    var library = list.closest('.template-library');
    var status = document.getElementById(type + '-template-status');
    var activeCount = Object.keys(APPLIED_TEMPLATE_IDS[type]).filter(function (id) { return APPLIED_TEMPLATE_IDS[type][id]; }).length;
    if (status) status.innerHTML = activeCount ? '<span class="template-active-chip">' + activeCount + ' active</span><button type="button" class="template-clear-active">Clear all</button>' : '';
    if (status) { var clear = status.querySelector('.template-clear-active'); if (clear) clear.addEventListener('click', function () { APPLIED_TEMPLATE_IDS[type] = {}; if (type === 'rx') { qsa('.rx-med-row[data-template-id], .lx-item-row[data-template-id]').forEach(function (row) { row.remove(); }); updateRxPreview(); } else { qsa('.dx-cb[data-template-id]').forEach(function (cb) { cb.checked = false; cb.removeAttribute('data-template-id'); }); updateDxSelectedCount(); updateDxPreview(); } buildTemplateLibraries(); }); }
    if (filters) {
        filters.innerHTML = ["All"].concat(templateCategories(type)).map(function (category) {
            var active = TEMPLATE_LIBRARY_STATE[type].category === category;
            return '<button type="button" class="template-filter-chip' + (active ? " active" : "") + '" aria-pressed="' + (active ? "true" : "false") + '" data-template-category="' + escHtml(category) + '">' + escHtml(category) + "</button>";
        }).join("");
        filters.querySelectorAll("[data-template-category]").forEach(function (button) {
            button.addEventListener("click", function () {
                TEMPLATE_LIBRARY_STATE[type].category = this.getAttribute("data-template-category");
                buildTemplateLibraries();
            });
        });
    }
    list.innerHTML = "";
    if (!templates.length) {
        list.innerHTML = '<div class="template-empty"><i data-feather="search"></i><strong>' + (allTemplates.length ? 'No templates match' : 'No templates yet') + '</strong><span>' + (allTemplates.length ? 'Clear filters or try another search.' : 'Save one from the form.') + '</span></div>';
        ri();
        return;
    }
    function section(title, items) {
        if (!items.length) return;
        var group = document.createElement('div'); group.className = 'template-library-section';
        group.innerHTML = '<span class="template-library-group-label" aria-hidden="true">' + escHtml(title) + ' <span>' + items.length + '</span></span><div class="template-library-section-list"></div>';
        var sectionList = group.querySelector('.template-library-section-list');
        items.forEach(function (t) { sectionList.appendChild(buildTemplateItem(t, type === "dx" ? applyDxTemplate : applyRxTemplate)); });
        list.appendChild(group);
    }
    var favorites = templates.filter(function (t) { return t.favorite || isTemplateFavorite(t.type, t.id); });
    var recent = templates.filter(function (t) { return t.lastUsedAt; }).sort(function (a,b) { return String(b.lastUsedAt).localeCompare(String(a.lastUsedAt)); }).slice(0, 5);
    var seen = {};
    section('Favorites', favorites); favorites.forEach(function (t) { seen[t.id] = true; });
    section('Recently used', recent.filter(function (t) { return !seen[t.id]; })); recent.forEach(function (t) { seen[t.id] = true; });
    section('All templates', templates);
    ri();
}

function buildTemplateLibraries() {
    buildTemplateLibrary("rx");
    buildTemplateLibrary("dx");
    lifestyleTemplateItems();
}

function buildRxTemplates() {
    buildTemplateLibraries();
}

function buildDxTemplates() {
    buildTemplateLibraries();
}

function applyRxTemplate(t) {
    if (!rxMedsList) return;
    t = normalizeTemplate(t);
    var replace = arguments[1] !== "merge";
    var meds = replace ? [] : collectRxMeds();
    if (replace) rxMedsList.innerHTML = "";
    (t.meds || []).forEach(function (m) {
        if (!replace && meds.some(function (existing) { return existing.name === m.name && existing.dose === m.dose; })) return;
        rxMedsList.appendChild(createMedRow(m, t.id));
    });
    if (rxDiagInput && (replace || !rxDiagInput.value.trim())) rxDiagInput.value = t.diag || "";
    if (lxItemsList) {
        if (replace) lxItemsList.innerHTML = "";
        (t.lifestyle || []).forEach(function (item) {
            if (!replace && collectLxItems().some(function (existing) { return existing.title === item.title; })) return;
            lxItemsList.appendChild(createLxItemRow(item, t.id));
        });
    }
    updateRxPreview();
    ri();
    showToast('Template "' + t.name + '" applied.');
}

function applyDxTemplate(t) {
    t = normalizeTemplate(t);
    var replace = arguments[1] !== "merge";
    if (replace) qsa(".dx-cb").forEach(function (cb) { cb.checked = false; });
    (t.tests || []).forEach(function (test) {
        var cb = qs('.dx-cb[data-test="' + test + '"]');
        if (cb) { cb.checked = true; cb.setAttribute('data-template-id', t.id); }
    });
    updateDxSelectedCount();
    updateDxPreview();
    showToast('Template "' + t.name + '" applied.');
}

function parseMedsText(text) {
    try {
        var parsed = JSON.parse(String(text || ""));
        if (Array.isArray(parsed)) return parsed;
    } catch (e) {
        // Existing templates may still have the original line-based format.
    }

    return String(text || "")
        .split(/(?:\r\n|\r|\n|,\s*(?=[^|,\n]+(?:\|[^|\n]*){3}))/)
        .map(function (line) {
            line = line.trim();
            if (!line) return null;
            var parts = line.split("|").map(function (p) {
                return p.trim();
            });
            return {
                name: parts[0] || "",
                dose: parts[1] || "",
                freq: parts[2]
                    ? parts[2]
                          .split(",")
                          .map(function (f) {
                              return f.trim();
                          })
                          .filter(Boolean)
                    : [],
                qty: parts[3] ? Number(parts[3]) || parts[3] : "",
            };
        })
        .filter(Boolean);
}

function parseTestsText(text) {
    try {
        var parsed = JSON.parse(String(text || ""));
        if (Array.isArray(parsed)) return parsed;
    } catch (e) {
        // Existing templates may still have the original text format.
    }

    return String(text || "")
        .split(/[\n,|]+/)
        .map(function (t) {
            return t.trim();
        })
        .filter(Boolean);
}

function templateMedLabel(med) {
    var frequency = Array.isArray(med.freq) ? med.freq.join(", ") : med.freq || "";
    return [
        med.name && "Medication: " + med.name,
        med.dose && "Dose: " + med.dose,
        frequency && "Frequency: " + frequency,
        med.qty && "Qty: " + med.qty,
    ].filter(Boolean).join(" · ");
}

function renderTemplateChipEditor(kind, items) {
    var listId = kind === "lifestyle" ? "tpl-lx-chip-list" : "tpl-" + kind + "-chip-list";
    var hiddenId = kind === "lifestyle" ? "tpl-lx-items" : "tpl-" + kind;
    var list = document.getElementById(listId);
    var hidden = document.getElementById(hiddenId);
    if (!list || !hidden) return;

    list.innerHTML = items.map(function (item, index) {
        var label;
        if (kind === "meds") {
            label = templateMedLabel(item);
        } else if (kind === "lifestyle") {
            label = [
                item.category,
                item.title,
                item.target && "Target: " + item.target,
                item.frequency && "Frequency: " + item.frequency,
                item.duration && "Duration: " + item.duration,
                item.instructions && "Instructions: " + item.instructions,
            ].filter(Boolean).join(" · ");
        } else {
            label = item;
        }
        return '<span class="template-chip">' + escHtml(label) +
            '<button type="button" class="template-chip-remove" data-index="' + index +
            '" aria-label="Remove ' + escHtml(label) + '">&times;</button></span>';
    }).join("");
    hidden.value = JSON.stringify(items);
    list.querySelectorAll(".template-chip-remove").forEach(function (button) {
        button.addEventListener("click", function () {
            var next = list._templateItems.slice();
            next.splice(Number(button.dataset.index), 1);
            list._templateItems = next;
            renderTemplateChipEditor(kind, next);
        });
    });
    list._templateItems = items;
}

function setTemplateChipEditor(kind, items) {
    var listId = kind === "lifestyle" ? "tpl-lx-chip-list" : "tpl-" + kind + "-chip-list";
    var list = document.getElementById(listId);
    if (!list) return;

    if (kind === "meds") {
        items = (items || []).map(function (med) {
            if (typeof med === "string") {
                var parts = med.split("|").map(function (part) { return part.trim(); });
                med = {
                    name: parts[0] || "",
                    dose: parts[1] || "",
                    freq: parts[2] || "",
                    qty: parts[3] || "",
                };
            }
            var frequency = med.freq || med.frequency || [];
            return {
                name: med.name || "",
                dose: med.dose || "",
                freq: Array.isArray(frequency) ? frequency : String(frequency),
                qty: med.qty || "",
            };
        }).filter(function (med) { return med.name; });
    } else if (kind === "lifestyle" && typeof items === "string") {
        items = parseLxItemsText(items);
    }

    renderTemplateChipEditor(kind, items || []);
    var addButton = document.getElementById({
        meds: "tpl-meds-add-btn",
        tests: "tpl-tests-add-btn",
        lifestyle: "tpl-lx-add-btn",
    }[kind]);
    var fieldIds = {
        meds: ["tpl-med-name", "tpl-med-dose", "tpl-med-frequency", "tpl-med-qty"],
        tests: ["tpl-test-entry"],
        lifestyle: [
            "tpl-lx-category",
            "tpl-lx-title",
            "tpl-lx-target",
            "tpl-lx-frequency",
            "tpl-lx-duration",
            "tpl-lx-instructions",
        ],
    }[kind];
    var fields = fieldIds.map(function (id) { return document.getElementById(id); });
    fields.forEach(function (field, index) {
        if (field && !(kind === "lifestyle" && index === 0)) field.value = "";
    });
    if (!addButton || addButton.dataset.chipEditorBound) return;
    addButton.dataset.chipEditorBound = "true";
    addButton.addEventListener("click", function () {
        var values = fields.map(function (field) {
            return field ? field.value.trim() : "";
        });
        var current = list._templateItems.slice();
        var item;
        if (kind === "meds") {
            item = {
                name: values[0],
                dose: values[1],
                freq: values[2] ? [values[2]] : [],
                qty: values[3] ? Number(values[3]) || values[3] : "",
            };
            if (!item.name) {
                fields[0].focus();
                return;
            }
            if (values[3] && (!/^\d+$/.test(values[3]) ||
                Number(values[3]) < 1 || Number(values[3]) > 100000)) {
                showToast("Quantity must be a whole number between 1 and 100000.");
                fields[3].focus();
                return;
            }
            current.push(item);
        } else if (kind === "lifestyle") {
            item = {
                category: values[0],
                title: values[1],
                target: values[2],
                frequency: values[3],
                duration: values[4],
                instructions: values[5],
            };
            if (!item.title) {
                fields[1].focus();
                return;
            }
            current.push(item);
        } else {
            item = values[0];
            if (!item) {
                fields[0].focus();
                return;
            }
            if (!current.some(function (test) {
                return test.toLowerCase() === item.toLowerCase();
            })) current.push(item);
        }
        list._templateItems = current;
        renderTemplateChipEditor(kind, current);
        fields.forEach(function (field, index) {
            if (!field) return;
            if (kind === "lifestyle" && index === 0) return;
            field.value = "";
        });
        fields[kind === "lifestyle" ? 1 : 0].focus();
    });
}

var LX_CATEGORIES = ["sleep", "exercise", "nutrition", "stress", "social", "other"];

var LX_CATEGORY_LABELS = {
    sleep: "Sleep",
    exercise: "Exercise",
    nutrition: "Nutrition",
    stress: "Stress / Mindfulness",
    social: "Social",
    other: "Other",
};

function parseLxItemsText(text) {
    try {
        var parsed = JSON.parse(String(text || ""));
        if (Array.isArray(parsed)) return parsed;
    } catch (e) {
        // Existing templates may still have the original line-based format.
    }

    return String(text || "")
        .split(/\n+/)
        .map(function (line) {
            var parts = line.split("|").map(function (p) {
                return p.trim();
            });
            if (!parts.length || !parts[0]) return null;
            var category = (parts[0] || "").toLowerCase();
            if (LX_CATEGORIES.indexOf(category) === -1) category = "other";
            return {
                category: category,
                title: parts[1] || "",
                target: parts[2] || "",
                frequency: parts[3] || "",
                duration: parts[4] || "",
                instructions: parts[5] || "",
            };
        })
        .filter(Boolean)
        .filter(function (item) {
            return item.title;
        });
}

function openTemplateModal(type, template) {
    setVal("tpl-id", template ? template.id : "");
    setVal("tpl-type", type === "lifestyle" ? "rx" : type);
    setVal("tpl-name", template ? template.name : "");
    setVal("tpl-tag", template ? template.tag : "");
    setVal("tpl-desc", template ? template.desc || "" : "");
    setVal("tpl-diag", template ? template.diag || "" : "");
    setTemplateChipEditor("meds", template ? template.meds || [] : []);
    setTemplateChipEditor("tests", template ? template.tests || [] : []);
    setTemplateChipEditor("lifestyle", template ? template.lifestyle || [] : []);

    var title = document.getElementById("template-modal-title");
    if (title)
        title.textContent =
            (template ? "Edit" : "Add") +
            (type === "dx"
                ? " Diagnostic Template"
                : type === "lifestyle"
                  ? " Lifestyle Template"
                  : " Rx Template");

    var diagWrap = document.getElementById("tpl-diag-wrap");
    var medsWrap = document.getElementById("tpl-meds-wrap");
    var testsWrap = document.getElementById("tpl-tests-wrap");
    var lifestyleWrap = document.getElementById("tpl-lifestyle-wrap");
    if (type === "lifestyle") {
        if (diagWrap) hide(diagWrap);
        if (medsWrap) hide(medsWrap);
        if (testsWrap) hide(testsWrap);
        if (lifestyleWrap) show(lifestyleWrap);
    } else if (type === "dx") {
        if (diagWrap) hide(diagWrap);
        if (medsWrap) hide(medsWrap);
        if (testsWrap) show(testsWrap);
        if (lifestyleWrap) hide(lifestyleWrap);
    } else {
        if (diagWrap) show(diagWrap);
        if (medsWrap) show(medsWrap);
        if (testsWrap) hide(testsWrap);
        if (lifestyleWrap) show(lifestyleWrap);
    }
    openModal("template-modal");
}

function deleteTemplate(t) {
    if (!t.id) return;
    showTemplateDeleteModal(t.name).then(function (confirmed) {
        if (!confirmed) return;
        performTemplateDelete(t);
    });
}

function showTemplateDeleteModal(templateName) {
    return new Promise(function (resolve) {
        var overlay = document.createElement("div");
        overlay.className = "template-choice-overlay template-choice-enter";
        overlay.setAttribute("role", "dialog");
        overlay.setAttribute("aria-modal", "true");
        overlay.setAttribute("aria-labelledby", "template-delete-title");
        overlay.innerHTML =
            '<div class="template-choice-modal template-delete-modal">' +
            '<button type="button" class="template-choice-close" aria-label="Close"><i data-feather="x"></i></button>' +
            '<div class="template-swal-icon">!</div>' +
            '<h3 id="template-delete-title">Are you sure?</h3>' +
            '<p>You can\'t revert this action. Delete <strong>' + escHtml(templateName) + '</strong>?</p>' +
            '<div class="template-choice-actions template-delete-actions">' +
            '<button type="button" class="template-choice-delete-confirm">Yes, delete it!</button>' +
            '<button type="button" class="template-choice-cancel">No, keep it!</button>' +
            '</div>' +
            '</div>';
        document.body.appendChild(overlay);
        ri();
        var closed = false;
        var close = function (confirmed) {
            if (closed) return;
            closed = true;
            overlay.classList.remove("template-choice-enter");
            overlay.classList.add("template-choice-closing");
            setTimeout(function () {
                overlay.remove();
                resolve(confirmed);
            }, 160);
        };
        overlay.querySelector(".template-choice-delete-confirm").addEventListener("click", function () { close(true); });
        overlay.querySelector(".template-choice-cancel").addEventListener("click", function () { close(false); });
        overlay.querySelector(".template-choice-close").addEventListener("click", function () { close(false); });
        overlay.addEventListener("click", function (event) { if (event.target === overlay) close(false); });
        overlay.addEventListener("keydown", function (event) { if (event.key === "Escape") close(false); });
        overlay.querySelector(".template-choice-delete-confirm").focus();
    });
}

function performTemplateDelete(t) {
    var base =
        (window.PSYCH_ROUTES || {}).templatesUpdate ||
        "/psychiatrist/clinical-templates";
    apiFetch(base + "/" + t.id, { method: "DELETE" })
        .then(function (data) {
            removeTemplateLocal(t.type, t.id);
            buildRxTemplates();
            buildDxTemplates();
            showToast(data.message || "Template deleted.");
        })
        .catch(function (err) {
            showToast(err.message);
        });
}

var rxTemplateAddBtn = document.getElementById("rx-template-add-btn");
if (rxTemplateAddBtn)
    rxTemplateAddBtn.addEventListener("click", function () {
        openTemplateModal("rx");
    });
var dxTemplateAddBtn = document.getElementById("dx-template-add-btn");
if (dxTemplateAddBtn)
    dxTemplateAddBtn.addEventListener("click", function () {
        openTemplateModal("dx");
    });

function saveCurrentFormAsTemplate(type) {
    var template = { type: type, name: "", tag: "", desc: "" };
    if (type === "rx") {
        template.diag = getVal("rx-diagnosis");
        template.meds = collectRxMeds().map(function (med) {
            return { name: med.name, dose: med.dose, freq: med.frequency, qty: med.qty };
        });
        template.lifestyle = collectLxItems();
    } else {
        template.tests = Array.prototype.map.call(qsa(".dx-cb:checked"), function (cb) { return cb.getAttribute("data-test"); });
    }
    openTemplateModal(type, template);
}

var saveRxTemplateBtn = document.getElementById("save-rx-template-btn");
if (saveRxTemplateBtn) saveRxTemplateBtn.addEventListener("click", function () { saveCurrentFormAsTemplate("rx"); });
var saveDxTemplateBtn = document.getElementById("save-dx-template-btn");
if (saveDxTemplateBtn) saveDxTemplateBtn.addEventListener("click", function () { saveCurrentFormAsTemplate("dx"); });

qsa(".template-search:not(#lifestyle-template-search)").forEach(function (input) {
    input.addEventListener("input", function () {
        var type = this.id.indexOf("dx-") === 0 ? "dx" : "rx";
        TEMPLATE_LIBRARY_STATE[type].query = this.value.trim();
        buildTemplateLibraries();
    });
});
qsa(".template-favorite-toggle:not(#lifestyle-template-favorites)").forEach(function (button) {
    button.addEventListener("click", function () {
        var type = this.closest(".template-library").getAttribute("data-template-type");
        var next = !TEMPLATE_LIBRARY_STATE[type].favoritesOnly;
        TEMPLATE_LIBRARY_STATE[type].favoritesOnly = next;
        this.setAttribute("aria-pressed", next ? "true" : "false");
        buildTemplateLibraries();
    });
});

var tplSaveBtn = document.getElementById("tpl-save-btn");
if (tplSaveBtn) {
    tplSaveBtn.addEventListener("click", function () {
        var id = getVal("tpl-id");
        var type = getVal("tpl-type") || "rx";
        var name = getVal("tpl-name").trim();
        if (!name) {
            showToast("Template name is required.");
            return;
        }

        var payload = {
            type: type,
            name: name,
            tag: getVal("tpl-tag").trim() || null,
            description: getVal("tpl-desc").trim() || null,
        };
        if (type === "dx") {
            payload.tests = parseTestsText(getVal("tpl-tests"));
        } else {
            payload.diag = getVal("tpl-diag").trim() || null;
            payload.meds = parseMedsText(getVal("tpl-meds"));
            payload.lifestyle = parseLxItemsText(getVal("tpl-lx-items"));

            if (!payload.description) {
                var descParts = payload.meds
                    .map(function (m) {
                        return m.name || "";
                    })
                    .concat(
                        payload.lifestyle.map(function (item) {
                            return item.title || "";
                        })
                    )
                    .filter(Boolean)
                    .slice(0, 4);
                if (descParts.length) {
                    payload.description = descParts.join(", ");
                }
            }
        }

        var routes = window.PSYCH_ROUTES || {};
        var url = id
            ? (routes.templatesUpdate || "/psychiatrist/clinical-templates") +
              "/" +
              id
            : routes.templatesStore || "/psychiatrist/clinical-templates";
        var method = id ? "PUT" : "POST";

        apiFetch(url, { method: method, body: JSON.stringify(payload) })
            .then(function (data) {
                upsertTemplateLocal(data.template);
                buildRxTemplates();
                buildDxTemplates();
                closeModal("template-modal");
                showToast(data.message || "Template saved.");
                if (window.refreshTemplateManage) window.refreshTemplateManage();
            })
            .catch(function (err) {
                showToast(err.message);
            });
    });
}

/* ============================================================
    RX: PRINT - formal Rx pad (matches reference layout)
   ============================================================ */
function escHtml(s) {
    return String(s == null ? "" : s)
        .replace(/&/g, "\u0026amp;")
        .replace(/</g, "\u0026lt;")
        .replace(/>/g, "\u0026gt;")
        .replace(/"/g, "\u0026quot;")
        .replace(/'/g, "\u0026#39;");
}

function collectRxMeds() {
    var meds = [];
    var rows = qsa(".rx-med-row", rxMedsList);
    rows.forEach(function (row) {
        var name = row.querySelector(".med-name-wrap input").value.trim();
        var dose = row.querySelector(".med-dose-input").value.trim();
        var frequencies = [];
        row.querySelectorAll(".freq-checkboxes input:checked").forEach(function (checkbox) {
            frequencies.push(checkbox.parentElement.textContent.trim());
        });
        var customFrequency = row.querySelector(".freq-custom-input").value.trim();
        if (customFrequency) frequencies.push(customFrequency);
        var frequency = frequencies.join(", ");
        var qty = row.querySelector(".med-qty-input").value;
        if (!name || (qty && (!/^\d+$/.test(qty) || Number(qty) < 1))) return;
        meds.push({
            name: name,
            dose: dose,
            frequency: frequency,
            qty: qty,
        });
    });
    return meds;
}

function cellCheckedRx(med, meal) {
    var f = (med.frequency || "").toLowerCase();
    if (meal === "Breakfast")
        return f.indexOf("breakfast") >= 0 || f.indexOf("morning") >= 0;
    if (meal === "Lunch") return f.indexOf("lunch") >= 0;
    if (meal === "Dinner") return f.indexOf("dinner") >= 0;
    return false;
}

function showPrintPreview(title, docHtml) {
    var frame = document.getElementById("rx-print-frame");
    if (!frame) return;
    var titleEl = document.getElementById("rx-print-modal-title");
    if (titleEl) titleEl.textContent = title;
    frame.srcdoc = docHtml;
    openModal("rx-print-modal");
}

function printPreviewFrame() {
    var frame = document.getElementById("rx-print-frame");
    if (!frame || !frame.contentWindow) return;
    frame.contentWindow.focus();
    frame.contentWindow.print();
}

(function () {
    var b = document.getElementById("rx-print-now-btn");
    if (b) b.addEventListener("click", printPreviewFrame);
})();

function rxPadStyles() {
    return (
        "<style>" +
        "@page{size:letter portrait;margin:0.5in;}" +
        "body{font-family:Arial,Helvetica,sans-serif;color:#000;font-size:13px;margin:0;}" +
        ".pad{width:100%;padding:0;}" +
        ".head{display:flex;align-items:center;gap:12px;}" +
        ".head-logo{flex-shrink:0;}.head-main{flex:1;text-align:center;}" +
        ".doc-name{font-size:19px;font-weight:900;text-transform:uppercase;}" +
        ".doc-role{font-size:13px;font-weight:700;margin-top:2px;}" +
        ".doc-dip{font-size:11.5px;line-height:1.5;}" +
        ".clinic-side{font-size:10px;font-weight:700;color:#16a34a;text-align:center;width:90px;flex-shrink:0;}" +
        ".contact-line{font-size:12.5px;font-style:italic;margin:10px 0 8px;}" +
        ".affil{font-size:12px;text-align:center;margin-bottom:10px;}" +
        ".affil-title{font-size:12px;margin-bottom:2px;}" +
        ".affil-cols{display:flex;flex-wrap:wrap;justify-content:center;gap:0 40px;}" +
        ".affil-cols span{width:46%;}" +
        ".dash{border-top:2px dashed #000;margin:8px 0;}" +
        ".fill-line,.fill-row{display:flex;gap:6px;align-items:flex-end;margin:14px 0;font-size:13px;}" +
        ".fill-row{gap:24px;}" +
        ".grow{flex:1;border-bottom:1px solid #000;min-height:16px;}" +
        ".fill-age{width:90px;}.fill-sex{width:110px;}" +
        "table.rx{width:100%;border-collapse:collapse;margin-top:16px;font-size:12px;}" +
        "table.rx th{border:1px solid #000;padding:3px 2px;font-size:10.5px;font-weight:700;background:#fff;}" +
        "table.rx th.grp{border-bottom:none;}" +
        "table.rx td{vertical-align:top;}" +
        ".meds-head{width:24%;}" +
        ".meds-head-inner{display:flex;align-items:center;justify-content:center;gap:6px;}" +
        ".rx-stamp-sm{font-size:26px;font-weight:900;font-style:italic;}" +
        ".meds-title{font-size:14px;font-weight:900;letter-spacing:1px;}" +
        ".vth{height:74px;max-width:26px;padding:2px;}" +
        ".vtext{display:inline-block;writing-mode:vertical-rl;transform:rotate(180deg);white-space:nowrap;}" +
        ".vtext-sub{font-weight:400;font-style:italic;font-size:10.5px;}" +
        ".vtext-main{font-weight:700;font-size:11px;}" +
        ".sig-block{margin-top:36px;page-break-inside:avoid;}" +
        ".sig-name{font-weight:700;font-size:13px;}.sig-lic{font-size:12px;}" +
        ".lx-block{margin-top:14px;page-break-inside:avoid;}" +
        ".lx-block-title{font-size:12px;font-weight:900;letter-spacing:1px;border-bottom:1px solid #000;padding-bottom:2px;margin-bottom:4px;}" +
        ".lx-cat{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin:6px 0 2px;}" +
        ".lx-row{padding:2px 0 2px 10px;border-left:2px solid #000;}" +
        ".lx-row-title{font-weight:700;font-size:12px;}" +
        ".lx-row-meta{font-weight:400;font-size:11px;}" +
        ".lx-row-instr{font-size:11px;font-style:italic;}" +
        ".rx-print-notes{margin-top:14px;padding-top:8px;border-top:1px solid #000;font-size:12px;line-height:1.4;}" +
        ".rx-print-notes-title{font-size:12px;font-weight:900;letter-spacing:1px;margin-bottom:4px;}" +
        ".foot{margin-top:14px;font-size:10px;color:#333;display:flex;justify-content:space-between;}" +
        "</style>"
    );
}

function buildRxMedRowsHtml(medList, fillerRows) {
    medList = medList || [];
    var targetRows = fillerRows == null ? 0 : fillerRows;
    var mealCols = ["Breakfast", "Lunch", "Dinner"];
    var whenCols = ["Before", "After"];
    var rows = "";

    medList.forEach(function (med) {
        var medLabel = med.name + (med.dose ? " " + med.dose : "");
        var cells = "";
        mealCols.forEach(function (meal) {
            var on = cellCheckedRx(med, meal);
            whenCols.forEach(function () {
                cells +=
                    '<td style="border:1px solid #000;text-align:center;">' +
                    (on ? "&#10003;" : "") +
                    "</td>";
            });
        });
        var bedtime =
            (med.frequency || "").toLowerCase().indexOf("bedtime") >= 0
                ? "&#10003;"
                : "";
        cells +=
            '<td style="border:1px solid #000;text-align:center;">' +
            bedtime +
            "</td>";
        cells +=
            '<td style="border:1px solid #000;text-align:center;">' +
            escHtml(med.qty || "") +
            "</td>";
        rows +=
            "<tr>" +
            '<td style="border:1px solid #000;padding:6px;vertical-align:top;">' +
            '<div style="font-weight:700;">' +
            escHtml(medLabel) +
            "</div>" +
            (med.frequency
                ? '<div style="font-size:11px;">' +
                  escHtml(med.frequency) +
                  "</div>"
                : "") +
            "</td>" +
            cells +
            "</tr>";
    });

    var emptyRows = Math.max(0, targetRows - medList.length);
    for (var i = 0; i < emptyRows; i++) {
        rows +=
            "<tr>" +
            '<td style="border:1px solid #000;height:34px;"></td>' +
            '<td style="border:1px solid #000;"></td><td style="border:1px solid #000;"></td>' +
            '<td style="border:1px solid #000;"></td><td style="border:1px solid #000;"></td>' +
            '<td style="border:1px solid #000;"></td><td style="border:1px solid #000;"></td>' +
            '<td style="border:1px solid #000;"></td><td style="border:1px solid #000;"></td>' +
            "</tr>";
    }
    return rows;
}

function buildRxPadDocument(
    P,
    doc,
    patientName,
    age,
    date,
    address,
    patientSex,
    medList,
    lifestyleList,
    notes,
) {
    medList = medList || [];
    lifestyleList = lifestyleList || [];
    notes = notes || "";
    var logoTag = P.clinicLogo
        ? '<img src="' +
          P.clinicLogo +
          '" style="width:64px;height:64px;object-fit:contain;border-radius:8px;" />'
        : "";
    var fillerRows = 0;
    var showMedGrid = medList.length > 0 || lifestyleList.length === 0;
    var lifestyleBlockHtml = lifestyleList.length
        ? '<div class="lx-block"><div class="lx-block-title">LIFESTYLE INTERVENTIONS</div>' +
          buildLxPadHtml(lifestyleList) +
          "</div>"
        : "";
    var notesBlockHtml = '<div class="rx-print-notes"><div class="rx-print-notes-title">INSTRUCTIONS</div><div>' + escHtml(notes.trim() || "\u2014") + '</div></div>';
    return (
        "<html><head><title>MB.EA Wellness Center Prescription</title>" +
        rxPadStyles() +
        "</head><body>" +
        '<div class="pad">' +
        '<div class="head">' +
        '<div class="head-logo">' +
        logoTag +
        "</div>" +
        '<div class="head-main">' +
        '<div class="doc-name">' +
        escHtml(doc.name || "\u2014") +
        "</div>" +
        '<div class="doc-role">Psychiatrist/Psychotherapist</div>' +
        '<div class="doc-dip">Diplomate of the Specialty Board of Philippine Psychiatry<br/>Diplomate, Philippine Psychiatric Association<br/>' +
        escHtml(doc.clinic_email || doc.email || "") +
        "</div>" +
        "</div>" +
        '<div class="clinic-side">' +
        escHtml(doc.clinic || "MB.EA Wellness Center") +
        '<br/><span style="font-weight:400;">Mental Health<br/>Mental Health & Wellness Clinic</span></div>' +
        "</div>" +
        '<div class="contact-line" style="text-align:center;">0905-071-3671</div>' +
        '<div class="affil"><div class="affil-title">Hospital Affiliations:</div>' +
        '<div class="affil-cols">' +
        "<span>Remedios Trinidad Romualdez Hospital</span><span>Divine Word Hospital</span>" +
        "<span>United Shalom Medical Center</span><span>Mother of Mercy Hospital</span>" +
        '<span style="width:100%;text-align:center;">ACE Medical Center (Room 433)</span>' +
        "</div></div>" +
        '<div class="dash"></div>' +
        '<div class="fill-line">Patient Name:<div class="grow" style="font-weight:600;text-align:center;">' +
        escHtml(patientName) +
        "</div>" +
        '<span style="margin-left:12px;">Date:</span><div class="fill-age" style="border-bottom:1px solid #000;text-align:center;">' +
        escHtml(date) +
        "</div></div>" +
        '<div class="fill-row">Address:<div class="grow" style="text-align:center;">' +
        escHtml(address) +
        "</div>" +
        '<span>Age:</span><div class="fill-age" style="border-bottom:1px solid #000;text-align:center;">' +
        escHtml(age) +
        "</div>" +
        '<span>Sex:</span><div class="fill-sex" style="border-bottom:1px solid #000;text-align:center;">' +
        escHtml(patientSex || "") +
        "</div></div>" +
        (showMedGrid
            ? '<table class="rx"><thead><tr>' +
              '<th rowspan="2" class="meds-head"><div class="meds-head-inner"><span class="rx-stamp-sm">&#8477;</span><span class="meds-title">MEDICATIONS</span></div></th>' +
              '<th class="grp" colspan="2">Breakfast</th><th class="grp" colspan="2">Lunch</th><th class="grp" colspan="2">Dinner</th>' +
              '<th rowspan="2" class="vth"><span class="vtext vtext-main">BEDTIME</span></th>' +
              '<th rowspan="2" class="vth"><span class="vtext vtext-main">QUANTITY</span></th></tr>' +
              "<tr>" +
              '<th class="vth"><span class="vtext vtext-sub">Before</span></th><th class="vth"><span class="vtext vtext-sub">After</span></th>' +
              '<th class="vth"><span class="vtext vtext-sub">Before</span></th><th class="vth"><span class="vtext vtext-sub">After</span></th>' +
              '<th class="vth"><span class="vtext vtext-sub">Before</span></th><th class="vth"><span class="vtext vtext-sub">After</span></th>' +
              "</tr></thead><tbody>" +
              buildRxMedRowsHtml(medList, fillerRows) +
              "</tbody></table>"
            : "") +
        lifestyleBlockHtml +
        notesBlockHtml +
        '<div class="sig-block">' +
        '<div class="sig-name">' +
        escHtml(doc.name || "\u2014") +
        "</div></div>" +
        '<div class="foot"><span>' +
        escHtml(doc.clinic || "MB.EA Wellness Center") +
        "</span><span>Printed " +
        escHtml(todayStr) +
        "</span></div>" +
        "</div></body></html>"
    );
}

window.printRx = function () {
    var patientId = document.getElementById("rx-patient").value;
    var patient = findPatientLocal(patientId);
    var P = window.PSYCH_DATA || {};
    var doc = P.prescriber || {};
    var medList = collectRxMeds();
    var lifestyleList = collectLxItems();

    if (!patientId) {
        showToast("Please select a patient first.");
        return;
    }
    if (!medList.length && !lifestyleList.length) {
        showToast(
            "Please add at least one medication or lifestyle intervention."
        );
        return;
    }

    var patientName = patient
        ? patient.name || "\u2014"
        : document.getElementById("rx-patient-search").value || "\u2014";
    var patientSex = patient && patient.sex ? patient.sex : "";
    var age = document.getElementById("rx-age").value || "";
    var date = document.getElementById("rx-date").value || todayStr;
    var address = patient && patient.address ? patient.address : "";

    showPrintPreview(
        "Print Preview \u2014 Prescription",
        buildRxPadDocument(
            P,
            doc,
            patientName,
            age,
            date,
            address,
            patientSex,
            medList,
            lifestyleList,
            document.getElementById("rx-notes").value || "",
        ),
    );
};

/* ============================================================
     DIAGNOSTIC REQUEST
  ============================================================ */
function buildDxChecklist() {
    var list = document.getElementById("dx-checklist");
    if (!list) return;
    list.innerHTML = "";
    DX_LAB_GROUPS.forEach(function (g) {
        var group = document.createElement("div");
        group.className = "dx-category-group";
        var header = document.createElement("div");
        header.className = "dx-cat-label";
        header.setAttribute("role", "button");
        header.setAttribute("tabindex", "0");
        header.innerHTML = '<span><i data-feather="chevron-down"></i>' + g.cat + '</span><span class="dx-cat-actions"><button type="button" class="dx-select-all">Select all</button><button type="button" class="dx-clear-all">Clear</button></span>';
        var items = document.createElement("div");
        items.className = "dx-category-items";
        header.addEventListener("click", function (event) {
            if (event.target.closest("button")) return;
            group.classList.toggle("collapsed");
        });
        header.addEventListener("keydown", function (event) {
            if (event.key === "Enter" || event.key === " ") { event.preventDefault(); group.classList.toggle("collapsed"); }
        });
        header.querySelector(".dx-select-all").addEventListener("click", function () {
            items.querySelectorAll(".dx-cb").forEach(function (cb) { cb.checked = true; });
            updateDxSelectedCount();
            updateDxPreview();
        });
        header.querySelector(".dx-clear-all").addEventListener("click", function () {
            items.querySelectorAll(".dx-cb").forEach(function (cb) { cb.checked = false; });
            updateDxSelectedCount();
            updateDxPreview();
        });
        group.appendChild(header);
        group.appendChild(items);
        g.tests.forEach(function (test) {
            var lbl = document.createElement("label");
            lbl.className = "dx-check-item";
            var cb = document.createElement("input");
            cb.type = "checkbox";
            cb.className = "dx-cb";
            cb.setAttribute("data-test", test);
            cb.addEventListener("change", function () { updateDxSelectedCount(); updateDxPreview(); });
            lbl.appendChild(cb);
            lbl.appendChild(document.createTextNode(test));
            items.appendChild(lbl);
        });
        list.appendChild(group);
    });
    ri();
    updateDxSelectedCount();
}

function updateDxSelectedCount() {
    var count = qsa(".dx-cb:checked").length;
    var counter = document.getElementById("dx-selected-count");
    if (counter) counter.textContent = count + " test" + (count === 1 ? "" : "s") + " selected";
}

function filterDxTests() {
    var query = getVal("dx-test-search").trim().toLowerCase();
    qsa(".dx-category-group").forEach(function (group) {
        var visible = 0;
        group.querySelectorAll(".dx-check-item").forEach(function (item) {
            var matches = !query || item.textContent.toLowerCase().indexOf(query) >= 0;
            item.hidden = !matches;
            if (matches) visible += 1;
        });
        group.hidden = visible === 0;
    });
}

var dxTestSearch = document.getElementById("dx-test-search");
if (dxTestSearch) dxTestSearch.addEventListener("input", filterDxTests);

var generateDxBtn = document.getElementById("generate-dx-btn");
if (generateDxBtn) {
    generateDxBtn.addEventListener("click", function () {
        var patientId = document.getElementById("dx-patient").value;
        var patient = findPatientLocal(patientId);
        var patientLabelText = patient
            ? patient.name || "\u2014"
            : document.getElementById("dx-patient-search").value || "\u2014";
        var date = document.getElementById("dx-date").value || todayStr;
        var notes = document.getElementById("dx-notes").value || "\u2014";

        document.getElementById("dx-prev-patient").textContent =
            patientLabelText;
        document.getElementById("dx-prev-date").textContent = date;
        document.getElementById("dx-prev-notes").textContent = notes;

        var testList = document.getElementById("dx-prev-tests");
        testList.innerHTML = "";
        qsa(".dx-cb:checked").forEach(function (cb) {
            var li = document.createElement("li");
            li.textContent = cb.getAttribute("data-test");
            testList.appendChild(li);
        });
        var otherImg = document.getElementById("dx-other-imaging");
        if (otherImg && otherImg.value.trim()) {
            var li2 = document.createElement("li");
            li2.textContent = otherImg.value.trim();
            testList.appendChild(li2);
        }

        if (!patientId) {
            showToast("Please select a patient first.");
            return;
        }
        showToast("Diagnostic request generated.");
    });
}

function buildDxPrintGridHtml() {
    var groups = [];
    qsa("#dx-checklist .dx-category-group").forEach(function (group) {
        var heading = group.querySelector(".dx-cat-label > span:first-child");
        var tests = Array.prototype.map.call(
            group.querySelectorAll(".dx-check-item"),
            function (item) {
                var checkbox = item.querySelector(".dx-cb");
                return {
                    name: checkbox
                        ? checkbox.getAttribute("data-test") || item.textContent.trim()
                        : item.textContent.trim(),
                    checked: !!(checkbox && checkbox.checked),
                };
            },
        ).filter(function (test) { return test.name; });
        groups.push({
            category: heading ? heading.textContent.trim() : "Laboratory",
            tests: tests,
        });
    });

    var imaging = document.querySelector(".dx-imaging-grid");
    if (imaging) {
        groups.push({
            category: "Imaging",
            tests: Array.prototype.map.call(
                imaging.querySelectorAll(".dx-check-item"),
                function (item) {
                    var checkbox = item.querySelector(".dx-cb");
                    return {
                        name: checkbox
                            ? checkbox.getAttribute("data-test") || item.textContent.trim()
                            : item.textContent.trim(),
                        checked: !!(checkbox && checkbox.checked),
                    };
                },
            ).filter(function (test) { return test.name; }),
        });
    }

    var groupsHtml = groups.map(function (group) {
        var testsHtml = group.tests.map(function (test) {
            return '<div class="dx-print-item"><span class="dx-print-box">' +
                (test.checked ? "&#9745;" : "&#9744;") +
                '</span><span>' + escHtml(test.name) + '</span></div>';
        }).join("");
        return '<section class="dx-print-group"><div class="dx-print-group-title">' +
            escHtml(group.category) + '</div><div class="dx-print-group-items">' +
            testsHtml + '</div></section>';
    }).join("");
    var otherImaging = getVal("dx-other-imaging").trim();

    return '<div class="dx-print-grid">' + groupsHtml + '</div>' +
        '<div class="dx-other-imaging"><strong>Other Imaging:</strong><span class="dx-write-line">' +
        (otherImaging ? escHtml(otherImaging) : "&nbsp;") + '</span></div>';
}

function buildDxPrintDocument() {
    var P = window.PSYCH_DATA || {};
    var doc = P.prescriber || {};
    var patientId = getVal("dx-patient");
    var patient = findPatientLocal(patientId);
    var patientName = patient ? patient.name || "—" : getVal("dx-patient-search") || "—";
    var age = getVal("dx-age") || "—";
    var date = getVal("dx-date") || todayStr;
    var notes = getVal("dx-notes") || "—";
    var logoTag = P.clinicLogo
        ? '<img src="' + P.clinicLogo + '" style="width:64px;height:64px;object-fit:contain;border-radius:8px;" />'
        : "";
    return (
        "<html><head><title>MB.EA Diagnostic Request</title>" +
        "<style>@page{size:letter portrait;margin:0.5in;}" +
        "*{box-sizing:border-box;}html,body{margin:0;}body{font-family:Arial,Helvetica,sans-serif;color:#000;font-size:13px;}" +
        ".pad{width:100%;padding:0;}" +
        ".head{display:flex;align-items:center;gap:12px;}.head-logo{flex-shrink:0;}.head-main{flex:1;text-align:center;}" +
        ".doc-name{font-size:19px;font-weight:900;text-transform:uppercase;}.doc-role{font-size:13px;font-weight:700;margin-top:2px;}" +
        ".doc-dip{font-size:11.5px;line-height:1.5;}.clinic-side{font-size:10px;font-weight:700;color:#16a34a;text-align:center;width:90px;flex-shrink:0;}" +
        ".contact-line{font-size:12.5px;font-style:italic;margin:10px 0 8px;text-align:center;}.affil{font-size:12px;text-align:center;margin-bottom:10px;}" +
        ".affil-title{font-size:12px;margin-bottom:2px;}.affil-cols{display:flex;flex-wrap:wrap;justify-content:center;gap:0 40px;}.affil-cols span{width:46%;}" +
        ".dash{border-top:2px dashed #000;margin:8px 0;}.patient{display:flex;gap:24px;align-items:flex-end;margin:14px 0;font-size:13px;}" +
        ".patient-main{display:flex;flex:1;gap:6px;align-items:flex-end;}.patient-age{width:90px;}.patient-date{width:120px;}.line{border-bottom:1px solid #000;min-height:16px;flex:1;text-align:center;font-weight:600;}" +
        ".section-title{font-size:12px;font-weight:900;letter-spacing:1px;border-bottom:1px solid #000;padding-bottom:2px;margin:14px 0 5px;}" +
        ".notes{white-space:pre-wrap;line-height:1.4;min-height:28px;}.dx-print-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:5px 9px;align-items:start;break-inside:avoid;page-break-inside:avoid;}" +
        ".dx-print-group{min-width:0;break-inside:avoid;page-break-inside:avoid;}.dx-print-group-title{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.35px;border-bottom:1px solid #000;padding-bottom:1px;margin:0 0 2px;}" +
        ".dx-print-group-items{display:grid;gap:1px;}.dx-print-item{display:flex;align-items:flex-start;gap:3px;min-width:0;font-size:8.5px;line-height:1.05;}.dx-print-box{width:10px;flex:0 0 10px;font-size:10px;line-height:1;}" +
        ".dx-other-imaging{display:flex;align-items:flex-end;gap:6px;margin-top:5px;font-size:9px;line-height:1.1;break-inside:avoid;page-break-inside:avoid;}.dx-other-imaging strong{white-space:nowrap;}.dx-write-line{flex:1;min-height:13px;border-bottom:1px solid #000;white-space:pre-wrap;overflow-wrap:anywhere;}.signature{margin-top:38px;border-top:1px solid #000;width:220px;padding-top:5px;font-weight:700;}" +
        ".foot{margin-top:14px;font-size:10px;color:#333;display:flex;justify-content:space-between;}" +
        '</style></head><body><div class="pad">' +
        '<div class="head"><div class="head-logo">' + logoTag + '</div><div class="head-main"><div class="doc-name">' + escHtml(doc.name || "—") + '</div><div class="doc-role">Psychiatrist/Psychotherapist</div><div class="doc-dip">Diplomate of the Specialty Board of Philippine Psychiatry<br/>Diplomate, Philippine Psychiatric Association<br/>' + escHtml(doc.clinic_email || doc.email || "") + '</div></div><div class="clinic-side">' + escHtml(doc.clinic || "MB.EA Wellness Center") + '<br/><span style="font-weight:400;">Mental Health<br/>Mental Health &amp; Wellness Clinic</span></div></div>' +
        '<div class="contact-line">0905-071-3671</div><div class="affil"><div class="affil-title">Hospital Affiliations:</div><div class="affil-cols"><span>Remedios Trinidad Romualdez Hospital</span><span>Divine Word Hospital</span><span>United Shalom Medical Center</span><span>Mother of Mercy Hospital</span><span style="width:100%;text-align:center;">ACE Medical Center (Room 433)</span></div></div><div class="dash"></div>' +
        '<div class="patient"><div class="patient-main">Patient Name:<div class="line">' + escHtml(patientName) + '</div></div><div class="patient-date">Date:<div class="line">' + escHtml(date) + '</div></div></div>' +
        '<div class="patient"><div class="patient-main">Requesting Physician:<div class="line">' + escHtml(doc.name || "—") + '</div></div><div class="patient-age">Age:<div class="line">' + escHtml(age) + '</div></div></div>' +
        '<div class="section-title">DIAGNOSTIC REQUEST FORM</div><div class="section-title">CLINICAL NOTES</div><div class="notes">' + escHtml(notes) + '</div><div class="section-title">TESTS ORDERED</div>' + buildDxPrintGridHtml() +
        '<div class="signature">' + escHtml(doc.name || "—") + '</div><div class="foot"><span>' + escHtml(doc.clinic || "MB.EA Wellness Center") + '</span><span>Printed ' + escHtml(todayStr) + '</span></div></div></body></html>'
    );
}

window.printDx = function () {
    showPrintPreview(
        "Print Preview \u2014 Diagnostic Request",
        buildDxPrintDocument(),
    );
};
/* ============================================================
     RX: LIFESTYLE INTERVENTIONS (part of the same prescription)
   ============================================================ */
var lxItemsList = document.getElementById("rx-lifestyle-list");
var addLxItemBtn = document.getElementById("add-lifestyle-btn");

function createLxItemRow(prefill, templateId) {
    prefill = prefill || {};
    var row = document.createElement("div");
    row.className = "lx-item-row";
    if (templateId) row.setAttribute("data-template-id", templateId);

    // Category
    var cat = document.createElement("select");
    cat.className = "field-input";
    LX_CATEGORIES.forEach(function (c) {
        var opt = document.createElement("option");
        opt.value = c;
        opt.textContent = LX_CATEGORY_LABELS[c];
        cat.appendChild(opt);
    });
    cat.value =
        LX_CATEGORIES.indexOf(prefill.category) >= 0
            ? prefill.category
            : "sleep";

    // Title
    var title = document.createElement("input");
    title.type = "text";
    title.className = "field-input";
    title.placeholder = "Intervention (e.g. Morning walk)...";
    title.value = prefill.title || "";

    // Target
    var target = document.createElement("input");
    target.type = "text";
    target.className = "field-input";
    target.placeholder = "e.g. 150 min/week";
    target.value = prefill.target || "";

    // Frequency
    var freq = document.createElement("input");
    freq.type = "text";
    freq.className = "field-input";
    freq.placeholder = "e.g. Daily / 5x per week";
    freq.value = prefill.frequency || "";

    // Duration
    var dur = document.createElement("input");
    dur.type = "text";
    dur.className = "field-input";
    dur.placeholder = "e.g. 8 weeks";
    dur.value = prefill.duration || "";

    // Instructions
    var instrWrap = document.createElement("div");
    instrWrap.className = "lx-item-instructions";
    var instr = document.createElement("textarea");
    instr.rows = 2;
    instr.placeholder = "Instructions (how, safety notes, tracking)...";
    instr.value = prefill.instructions || "";
    instrWrap.appendChild(instr);

    // Delete (always removes the row)
    var delBtn = document.createElement("button");
    delBtn.type = "button";
    delBtn.className = "med-del-btn";
    delBtn.innerHTML = '<i data-feather="trash-2"></i>';
    delBtn.addEventListener("click", function () {
        row.remove();
        ri();
    });

    row.appendChild(cat);
    row.appendChild(title);
    row.appendChild(target);
    row.appendChild(freq);
    row.appendChild(dur);
    row.appendChild(delBtn);
    row.appendChild(instrWrap);
    ri();
    return row;
}

if (lxItemsList && addLxItemBtn) {
    addLxItemBtn.addEventListener("click", function () {
        var empty = lxItemsList.querySelector(".lx-empty-state");
        if (empty) empty.remove();
        lxItemsList.appendChild(createLxItemRow());
    });
}

function collectLxItems() {
    var items = [];
    if (!lxItemsList) return items;
    qsa(".lx-item-row", lxItemsList).forEach(function (row) {
        var category = row.querySelector("select").value;
        var inputs = row.querySelectorAll("input.field-input");
        var title = inputs[0] ? inputs[0].value.trim() : "";
        if (!title) return;
        items.push({
            category: category,
            title: title,
            target: inputs[1] ? inputs[1].value.trim() : "",
            frequency: inputs[2] ? inputs[2].value.trim() : "",
            duration: inputs[3] ? inputs[3].value.trim() : "",
            instructions: row.querySelector("textarea")
                ? row.querySelector("textarea").value.trim()
                : "",
        });
    });
    return items;
}

function lifestyleTemplateItems() {
    var list = document.getElementById("lifestyle-template-list");
    if (!list) return;
    var query = getVal("lifestyle-template-search").toLowerCase();
    var allTemplates = RX_TEMPLATES
        .map(normalizeTemplate)
        .filter(function (template) { return (template.lifestyle || []).length > 0; });
    var categoryFor = function (template) {
        var firstItem = template.lifestyle[0];
        return template.tag || LX_CATEGORY_LABELS[firstItem.category] || "General";
    };
    var categories = allTemplates
        .map(categoryFor)
        .filter(function (category, index, values) { return values.indexOf(category) === index; })
        .sort();
    var filterContainer = document.getElementById("lifestyle-template-filters");
    var currentCategory = filterContainer ? filterContainer.getAttribute("data-active-category") || "All" : "All";
    if (filterContainer) {
        filterContainer.innerHTML = ["All"].concat(categories).map(function (category) {
            var active = currentCategory === category;
            return '<button type="button" class="template-filter-chip' + (active ? " active" : "") +
                '" aria-pressed="' + (active ? "true" : "false") + '" data-template-category="' +
                escHtml(category) + '">' + escHtml(category) + "</button>";
        }).join("");
        filterContainer.querySelectorAll("[data-template-category]").forEach(function (button) {
            button.addEventListener("click", function () {
                filterContainer.setAttribute("data-active-category", this.getAttribute("data-template-category"));
                lifestyleTemplateItems();
            });
        });
    }
    var favoritesOnly = document.getElementById("lifestyle-template-favorites")
        ? document.getElementById("lifestyle-template-favorites").getAttribute("aria-pressed") === "true"
        : false;
    var templates = allTemplates.filter(function (template) {
        return (!query || templateSearchText(template).indexOf(query) >= 0) &&
            (currentCategory === "All" || categoryFor(template) === currentCategory) &&
            (!favoritesOnly || template.favorite || isTemplateFavorite("rx", template.id));
    });

    list.innerHTML = "";
    if (!templates.length) {
        list.innerHTML = '<div class="template-empty"><i data-feather="search"></i><strong>' +
            (allTemplates.length ? "No templates match" : "No lifestyle templates yet") +
            '</strong><span>' + (allTemplates.length ? "Clear filters or try another search." : "Save one from the form.") + "</span></div>";
        ri();
        return;
    }

    function section(title, items) {
        if (!items.length) return;
        var group = document.createElement("div");
        group.className = "template-library-section";
        group.innerHTML = '<span class="template-library-group-label" aria-hidden="true">' +
            escHtml(title) + ' <span>' + items.length + '</span></span><div class="template-library-section-list"></div>';
        var sectionList = group.querySelector(".template-library-section-list");
        items.forEach(function (template) {
            sectionList.appendChild(buildTemplateItem(template, function (selected) {
                applyRxTemplate(Object.assign({}, selected, { meds: [], diag: "" }), "merge");
            }));
        });
        list.appendChild(group);
    }

    var favorites = templates.filter(function (template) {
        return template.favorite || isTemplateFavorite("rx", template.id);
    });
    var recent = templates
        .filter(function (template) { return template.lastUsedAt; })
        .sort(function (a, b) { return String(b.lastUsedAt).localeCompare(String(a.lastUsedAt)); })
        .slice(0, 5);
    var seen = {};
    section("Favorites", favorites);
    favorites.forEach(function (template) { seen[template.id] = true; });
    section("Recently used", recent.filter(function (template) { return !seen[template.id]; }));
    recent.forEach(function (template) { seen[template.id] = true; });
    section("All lifestyle templates", templates);
    ri();
}

function buildLifestylePrintDocument() {
    var patient = findPatientLocal(getVal("rx-patient"));
    var interventions = collectLxItems();
    var P = window.PSYCH_DATA || {};
    var doc = P.prescriber || {};
    var styles =
        "body{font-family:Arial,sans-serif;margin:0;padding:32px;color:#111827;} " +
        ".plan{max-width:760px;margin:auto;border:1px solid #cbd5e1;padding:28px;} " +
        ".clinic{font-size:20px;font-weight:800;text-align:center;} " +
        ".meta{display:flex;justify-content:space-between;margin:20px 0;border-bottom:1px solid #cbd5e1;padding-bottom:10px;} " +
        ".lx-cat{font-size:12px;font-weight:800;text-transform:uppercase;margin-top:14px;} " +
        ".lx-row{border-left:2px solid #2563eb;padding:4px 0 4px 10px;margin:5px 0;} " +
        ".lx-title{font-weight:700;} .lx-meta{color:#475569;font-size:12px;} " +
        ".lx-instructions{font-size:12px;margin-top:3px;} " +
        ".signature{text-align:right;margin-top:36px;} ";
    var empty = !interventions.length
        ? '<p>No lifestyle interventions have been added.</p>'
        : buildLxPadHtml(interventions);

    return "<html><head><title>Lifestyle Intervention</title><style>" + styles +
        "</style></head><body><div class='plan'>" +
        "<div class='clinic'>" + escHtml(doc.clinic || "MB.EA Wellness Center") + "</div>" +
        "<div class='meta'><span>Patient: <strong>" + escHtml(patient ? patient.name : "\u2014") + "</strong></span>" +
        "<span>Date: <strong>" + escHtml(displayDate(getVal("rx-date"))) + "</strong></span></div>" +
        empty +
        "<div class='signature'>" + escHtml(doc.name || "\u2014") +
        (doc.license_no ? "<br/><small>License No. " + escHtml(doc.license_no) + "</small>" : "") +
        "</div></div></body></html>";
}

var printLifestyleFormBtn = document.getElementById("print-lifestyle-form-btn");
if (printLifestyleFormBtn) {
    printLifestyleFormBtn.addEventListener("click", printLifestylePlan);
}

function printLifestylePlan() {
    if (!getVal("rx-patient")) {
        showToast("Please select a patient first.");
        return;
    }
    if (!collectLxItems().length) {
        showToast("Please add at least one lifestyle intervention.");
        return;
    }
    showPrintPreview(
        "Print Preview - Lifestyle Intervention",
        buildLifestylePrintDocument(),
    );
}

var saveLifestyleTemplateBtn = document.getElementById("save-lifestyle-template-btn");
if (saveLifestyleTemplateBtn) {
    saveLifestyleTemplateBtn.addEventListener("click", function () {
        var interventions = collectLxItems();
        if (!interventions.length) {
            showToast("Add at least one lifestyle intervention before saving a template.");
            return;
        }
        openTemplateModal("lifestyle", {
            name: "",
            desc: "",
            diag: "",
            meds: [],
            lifestyle: interventions,
        });
    });
}

var lifestyleTemplateSearch = document.getElementById("lifestyle-template-search");
if (lifestyleTemplateSearch) {
    lifestyleTemplateSearch.addEventListener("input", lifestyleTemplateItems);
}

var lifestyleTemplateFavorites = document.getElementById("lifestyle-template-favorites");
if (lifestyleTemplateFavorites) {
    lifestyleTemplateFavorites.addEventListener("click", function () {
        var next = this.getAttribute("aria-pressed") !== "true";
        this.setAttribute("aria-pressed", next ? "true" : "false");
        lifestyleTemplateItems();
    });
}

/* On-screen preview, grouped by category */
function buildLxGroupedHtml(items) {
    var html = "";
    LX_CATEGORIES.forEach(function (cat) {
        var group = (items || []).filter(function (it) {
            return it.category === cat;
        });
        if (!group.length) return;
        html +=
            '<div class="lx-prev-group-label">' +
            escHtml(LX_CATEGORY_LABELS[cat]) +
            "</div>";
        group.forEach(function (it) {
            var meta = [it.target, it.frequency, it.duration]
                .filter(Boolean)
                .map(escHtml)
                .join(" &#183; ");
            html +=
                '<div class="lx-prev-item">' +
                '<div class="lx-prev-item-title">' +
                escHtml(it.title) +
                "</div>" +
                (meta
                    ? '<div class="lx-prev-item-meta">' + meta + "</div>"
                    : "") +
                (it.instructions
                    ? '<div class="lx-prev-item-instr">' +
                      escHtml(it.instructions) +
                      "</div>"
                    : "") +
                "</div>";
        });
    });
    return html;
}

/* Printed pad block, grouped by category (matches the Rx pad style) */
function buildLxPadHtml(items) {
    var html = "";
    LX_CATEGORIES.forEach(function (cat) {
        var group = (items || []).filter(function (it) {
            return it.category === cat;
        });
        if (!group.length) return;
        html +=
            '<div class="lx-cat">' +
            escHtml(LX_CATEGORY_LABELS[cat]) +
            "</div>";
        group.forEach(function (it) {
            var meta = [it.target, it.frequency, it.duration]
                .filter(Boolean)
                .map(escHtml)
                .join(" &#183; ");
            html +=
                '<div class="lx-row">' +
                '<div class="lx-row-title">' +
                escHtml(it.title) +
                (meta
                    ? '<span class="lx-row-meta"> &#8212; ' + meta + "</span>"
                    : "") +
                "</div>" +
                (it.instructions
                    ? '<div class="lx-row-instr">' +
                      escHtml(it.instructions) +
                      "</div>"
                    : "") +
                "</div>";
        });
    });
    return html;
}

function displayDate(value) {
    if (!value) return "\u2014";
    var date = new Date(value + "T00:00:00");
    return isNaN(date.getTime()) ? value : date.toLocaleDateString("en-US", { month: "long", day: "numeric", year: "numeric" });
}

function updateRxPreview() {
    var patientId = getVal("rx-patient");
    var patient = findPatientLocal(patientId);
    var medications = collectRxMeds();
    var interventions = collectLxItems();
    var medsList = document.getElementById("preview-meds-list");
    if (medsList) {
        medsList.innerHTML = "";
        medications.forEach(function (med) {
            var li = document.createElement("li");
            li.textContent = med.name + (med.dose ? " " + med.dose : "") + (med.frequency ? " \u2014 " + med.frequency : "") + (med.qty ? " \u00b7 Qty: " + med.qty : "");
            medsList.appendChild(li);
        });
    }
    var lifestyleList = document.getElementById("preview-lifestyle-list");
    if (lifestyleList) lifestyleList.innerHTML = interventions.length ? buildLxGroupedHtml(interventions) : "";
    var lifestyleLabel = document.getElementById("preview-lifestyle-label");
    if (lifestyleLabel) interventions.length ? show(lifestyleLabel) : hide(lifestyleLabel);
    var patientLabel = patient ? patient.name : "\u2014";
    var setText = function (id, value) { var el = document.getElementById(id); if (el) el.textContent = value || "\u2014"; };
    setText("preview-patient", patientLabel);
    setText("preview-age", getVal("rx-age"));
    setText("preview-date", displayDate(getVal("rx-date")));
    setText("preview-diag", getVal("rx-diagnosis"));
    setText("preview-notes", getVal("rx-notes"));
    updateGenerateButtons();
}

function updateDxPreview() {
    var patient = findPatientLocal(getVal("dx-patient"));
    var setText = function (id, value) { var el = document.getElementById(id); if (el) el.textContent = value || "\u2014"; };
    setText("dx-prev-patient", patient ? patient.name : "\u2014");
    setText("dx-prev-date", displayDate(getVal("dx-date")));
    setText("dx-prev-notes", getVal("dx-notes"));
    var list = document.getElementById("dx-prev-tests");
    if (list) {
        list.innerHTML = "";
        qsa(".dx-cb:checked").forEach(function (cb) {
            var li = document.createElement("li");
            li.textContent = cb.getAttribute("data-test");
            list.appendChild(li);
        });
        var other = getVal("dx-other-imaging").trim();
        if (other) { var otherLi = document.createElement("li"); otherLi.textContent = other; list.appendChild(otherLi); }
    }
}

function updateGenerateButtons() {
    var rxButton = document.getElementById("generate-rx-btn");
    if (rxButton) rxButton.disabled = !getVal("rx-patient") || (!collectRxMeds().length && !collectLxItems().length);
    var dxButton = document.getElementById("generate-dx-btn");
    if (dxButton) dxButton.disabled = !getVal("dx-patient");
}

qsa("#rx-patient,#rx-patient-search,#rx-age,#rx-date,#rx-diagnosis,#rx-notes,#dx-patient,#dx-patient-search,#dx-age,#dx-date,#dx-notes,#dx-other-imaging").forEach(function (field) {
    field.addEventListener("input", function () { updateRxPreview(); updateDxPreview(); });
    field.addEventListener("change", function () { updateRxPreview(); updateDxPreview(); });
});
document.addEventListener("click", function (event) {
    if (!event.target.closest(".template-overflow-wrap")) {
        qsa(".template-menu.open").forEach(function (menu) { menu.classList.remove("open"); });
    }
});
document.addEventListener("input", function (event) {
    var row = event.target.closest(".rx-med-row, .lx-item-row");
    if (row && row.dataset.templateId) row.dataset.templateEdited = "true";
    if (row) updateRxPreview();
});
document.addEventListener("change", function (event) {
    var row = event.target.closest(".rx-med-row, .lx-item-row");
    if (row && row.dataset.templateId) row.dataset.templateEdited = "true";
    if (event.target.classList.contains("dx-cb") && event.target.dataset.templateId) event.target.dataset.templateEdited = "true";
    if (row) updateRxPreview();
});
updateRxPreview();
updateDxPreview();
