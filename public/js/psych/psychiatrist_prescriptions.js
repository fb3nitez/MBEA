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
        ri();
    });
});

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

function createMedRow(prefill) {
    prefill = prefill || {};
    var row = document.createElement("div");
    row.className = "rx-med-row";

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

    // Dosage
    var doseSelect = document.createElement("select");
    doseSelect.className = "field-input";
    DOSAGES.forEach(function (d) {
        var opt = document.createElement("option");
        opt.value = d;
        opt.textContent = d;
        if (prefill.dose === d) opt.selected = true;
        doseSelect.appendChild(opt);
    });
    // Custom input
    var customDoseInput = document.createElement("input");
    customDoseInput.type = "text";
    customDoseInput.className = "field-input hidden";
    customDoseInput.placeholder = "Custom dosage...";
    customDoseInput.style.marginTop = "4px";
    var doseWrap = document.createElement("div");
    doseWrap.appendChild(doseSelect);
    doseWrap.appendChild(customDoseInput);
    doseSelect.addEventListener("change", function () {
        if (this.value === "Custom") show(customDoseInput);
        else hide(customDoseInput);
        validateMedicationRow(row);
    });
    customDoseInput.addEventListener("input", function () { validateMedicationRow(row); });

    // Frequency
    var freqCol = document.createElement("div");
    freqCol.className = "med-freq-col";
    var freqCheckboxes = document.createElement("div");
    freqCheckboxes.className = "freq-checkboxes";
    var FREQS = ["Morning", "Breakfast", "Lunch", "Dinner", "Bedtime"];
    FREQS.forEach(function (f) {
        var lbl = document.createElement("label");
        lbl.className = "freq-cb-label";
        var cb = document.createElement("input");
        cb.type = "checkbox";
        if (prefill.freq && prefill.freq.includes(f)) cb.checked = true;
        lbl.appendChild(cb);
        lbl.appendChild(document.createTextNode(f));
        freqCheckboxes.appendChild(lbl);
    });
    var freqCustom = document.createElement("input");
    freqCustom.type = "text";
    freqCustom.className = "freq-custom-input";
    freqCustom.placeholder = "Custom (e.g. PRN)...";
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
    delBtn.innerHTML = '<i data-feather="x"></i>';
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
    var doseSelect = row.querySelector("select");
    var customDose = row.querySelector('input[placeholder="Custom dosage..."]');
    var qty = row.querySelector(".med-qty-input");
    var valid = true;
    if (doseSelect && doseSelect.value === "Custom" && customDose && !customDose.value.trim()) {
        customDose.classList.add("field-invalid");
        valid = false;
    } else if (customDose) {
        customDose.classList.remove("field-invalid");
    }
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
            var name = row.querySelector('input[type="text"]').value;
            var sel = row.querySelector("select");
            var dose = sel ? sel.value : "";
            if (dose === "Custom") {
                var ci = row.querySelectorAll('input[type="text"]')[1];
                dose = ci ? ci.value : "";
            }
            var freq = [];
            row.querySelectorAll('input[type="checkbox"]:checked').forEach(
                function (cb) {
                    freq.push(cb.parentElement.textContent.trim());
                },
            );
            var customFreq = row.querySelector(".freq-custom-input");
            if (customFreq && customFreq.value.trim())
                freq.push(customFreq.value.trim());
            var qty = row.querySelector(".med-qty-input").value;
            if (!name) return;
            var med = {
                name: name,
                dose: dose,
                frequency: freq.join(", "),
                qty: qty,
            };
            medications.push(med);
            var li = document.createElement("li");
            li.textContent =
                name +
                " " +
                dose +
                (freq.length ? " \u2014 " + freq.join(", ") : "") +
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
        onApply(t, mode);
        APPLIED_TEMPLATE_IDS[t.type][String(t.id)] = true;
        buildTemplateLibraries();
    });
}

function buildTemplateItem(t, onApply) {
    var div = document.createElement("div");
    div.className = "template-item";
    var favorite = isTemplateFavorite(t.type, t.id);
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
    var applyButton = applied
        ? '<button type="button" class="template-apply-target template-applied-button" disabled aria-disabled="true" title="Applied"><i data-feather="check"></i><span class="sr-only">Applied</span></button>'
        : '<button type="button" class="template-apply-target" aria-label="Apply ' + escHtml(t.name || "Template") + '" title="Apply template"><i data-feather="play"></i><span class="sr-only">Apply</span></button>';
    div.innerHTML =
        '<div class="template-item-top">' +
        applyButton +
        '<span class="template-heading"><span class="template-name">' + escHtml(t.name || "Template") + '</span>' +
        (t.tag
            ? '<span class="template-tag ' +
              (t.tagClass || "") +
              '">' +
              escHtml(t.tag) +
              "</span>"
            : "") +
          '</span>' +
        '<button type="button" class="template-favorite" aria-label="' + (favorite ? "Remove from favorites" : "Add to favorites") + '" aria-pressed="' + (favorite ? "true" : "false") + '"><i data-feather="star"></i></button>' +
        '<div class="template-overflow-wrap"><button type="button" class="template-overflow" aria-label="More actions" aria-expanded="false"><i data-feather="more-horizontal"></i></button>' +
        '<div class="template-menu"><button type="button" class="tpl-edit">Edit</button><button type="button" class="tpl-delete">Delete</button></div></div>' +
        "</div>" +
        '<div class="template-desc">' +
        escHtml(summary || "No details provided") +
        "</div>" +
        '<details class="template-preview"><summary>Preview details</summary><ul>' + (entries || "<li>No details provided</li>") + "</ul></details>" +
        "";
    if (!applied) {
        div.querySelector(".template-apply-target").addEventListener("click", function () {
            applyTemplateWithChoice(t, onApply);
        });
    }
    div.addEventListener("click", function (event) {
        if (applied) return;
        if (event.target.closest("button, details, summary, .template-menu")) return;
        applyTemplateWithChoice(t, onApply);
    });
    div.addEventListener("keydown", function (event) {
        if (applied) return;
        if ((event.key === "Enter" || event.key === " ") && event.target === div) {
            event.preventDefault();
            applyTemplateWithChoice(t, onApply);
        }
    });
    div.tabIndex = 0;
    div.setAttribute("role", "button");
    div.querySelector(".template-favorite").addEventListener("click", function () {
        var next = !isTemplateFavorite(t.type, t.id);
        setTemplateFavorite(t.type, t.id, next);
        buildTemplateLibraries();
    });
    div.querySelector(".template-overflow").addEventListener("click", function (event) {
        event.stopPropagation();
        var menu = div.querySelector(".template-menu");
        var open = menu.classList.toggle("open");
        this.setAttribute("aria-expanded", open ? "true" : "false");
    });
    div.querySelector(".tpl-edit").addEventListener("click", function () {
        openTemplateModal(t.type, t);
    });
    div.querySelector(".tpl-delete").addEventListener("click", function () {
        deleteTemplate(t);
    });
    return div;
}

function buildTemplateLibrary(type) {
    var list = document.getElementById(type + "-template-list");
    var filters = document.getElementById(type + "-template-filters");
    if (!list) return;
    var templates = (type === "dx" ? DX_TEMPLATES : RX_TEMPLATES).map(normalizeTemplate).filter(templateMatches);
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
        list.innerHTML = '<div class="template-empty"><i data-feather="search"></i><strong>No templates match</strong><span>Try another search or category.</span></div>';
        return;
    }
    templates.forEach(function (t) {
        list.appendChild(buildTemplateItem(t, type === "dx" ? applyDxTemplate : applyRxTemplate));
    });
    ri();
}

function buildTemplateLibraries() {
    buildTemplateLibrary("rx");
    buildTemplateLibrary("dx");
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
        rxMedsList.appendChild(createMedRow(m));
    });
    if (rxDiagInput && (replace || !rxDiagInput.value.trim())) rxDiagInput.value = t.diag || "";
    if (lxItemsList) {
        if (replace) lxItemsList.innerHTML = "";
        (t.lifestyle || []).forEach(function (item) {
            if (!replace && collectLxItems().some(function (existing) { return existing.title === item.title; })) return;
            lxItemsList.appendChild(createLxItemRow(item));
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
        if (cb) cb.checked = true;
    });
    updateDxSelectedCount();
    updateDxPreview();
    showToast('Template "' + t.name + '" applied.');
}

function parseMedsText(text) {
    return String(text || "")
        .split("\n")
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

function medsToText(meds) {
    return (meds || [])
        .map(function (m) {
            return [
                m.name || "",
                m.dose || "",
                Array.isArray(m.freq) ? m.freq.join(", ") : m.freq || "",
                m.qty || "",
            ].join(" | ");
        })
        .join("\n");
}

function parseTestsText(text) {
    return String(text || "")
        .split(/[\n,]+/)
        .map(function (t) {
            return t.trim();
        })
        .filter(Boolean);
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

function lxItemsToText(items) {
    return (items || [])
        .map(function (item) {
            return [
                item.category || "",
                item.title || "",
                item.target || "",
                item.frequency || "",
                item.duration || "",
                item.instructions || "",
            ].join(" | ");
        })
        .join("\n");
}

function openTemplateModal(type, template) {
    setVal("tpl-id", template ? template.id : "");
    setVal("tpl-type", type);
    setVal("tpl-name", template ? template.name : "");
    setVal("tpl-tag", template ? template.tag : "");
    setVal("tpl-desc", template ? template.desc || "" : "");
    setVal("tpl-diag", template ? template.diag || "" : "");
    setVal("tpl-meds", template ? medsToText(template.meds) : "");
    setVal("tpl-tests", template ? (template.tests || []).join(", ") : "");
    setVal("tpl-lx-items", template ? lxItemsToText(template.lifestyle) : "");

    var title = document.getElementById("template-modal-title");
    if (title)
        title.textContent =
            (template ? "Edit" : "Add") +
            (type === "dx" ? " Diagnostic Template" : " Rx Template");

    var diagWrap = document.getElementById("tpl-diag-wrap");
    var medsWrap = document.getElementById("tpl-meds-wrap");
    var testsWrap = document.getElementById("tpl-tests-wrap");
    var lifestyleWrap = document.getElementById("tpl-lifestyle-wrap");
    if (type === "dx") {
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

qsa(".template-search").forEach(function (input) {
    input.addEventListener("input", function () {
        var type = this.id.indexOf("dx-") === 0 ? "dx" : "rx";
        TEMPLATE_LIBRARY_STATE[type].query = this.value.trim();
        buildTemplateLibraries();
    });
});
qsa(".template-favorite-toggle").forEach(function (button) {
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
        var name = row.querySelector('input[type="text"]').value;
        var sel = row.querySelector("select");
        var dose = sel ? sel.value : "";
        if (dose === "Custom") {
            var ci = row.querySelectorAll('input[type="text"]')[1];
            dose = ci ? ci.value : "";
        }
        var freq = [];
        row.querySelectorAll('input[type="checkbox"]:checked').forEach(
            function (cb) {
                freq.push(cb.parentElement.textContent.trim());
            },
        );
        var customFreq = row.querySelector(".freq-custom-input");
        var customFreqVal =
            customFreq && customFreq.value.trim()
                ? customFreq.value.trim()
                : "";
        var qty = row.querySelector(".med-qty-input").value;
        if (!name || (qty && (!/^\d+$/.test(qty) || Number(qty) < 1))) return;
        meds.push({
            name: name,
            dose: dose,
            frequency: freq.join(", "),
            customFreq: customFreqVal,
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
        "@page{size:letter portrait;margin:14mm;}" +
        "body{font-family:Arial,Helvetica,sans-serif;color:#000;font-size:13px;margin:0;}" +
        ".pad{border:2px solid #000;padding:16px 18px;}" +
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
        ".sig-bar{width:200px;border-top:1.5px solid #000;margin-bottom:4px;}" +
        ".sig-name{font-weight:700;font-size:13px;}.sig-lic{font-size:12px;}" +
        ".lx-block{margin-top:14px;page-break-inside:avoid;}" +
        ".lx-block-title{font-size:12px;font-weight:900;letter-spacing:1px;border-bottom:1px solid #000;padding-bottom:2px;margin-bottom:4px;}" +
        ".lx-cat{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin:6px 0 2px;}" +
        ".lx-row{padding:2px 0 2px 10px;border-left:2px solid #000;}" +
        ".lx-row-title{font-weight:700;font-size:12px;}" +
        ".lx-row-meta{font-weight:400;font-size:11px;}" +
        ".lx-row-instr{font-size:11px;font-style:italic;}" +
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
            (med.customFreq
                ? '<div style="font-size:11px;">' +
                  escHtml(med.customFreq) +
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
) {
    medList = medList || [];
    lifestyleList = lifestyleList || [];
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
        '<div class="sig-block"><div class="sig-bar"></div>' +
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

function buildDxPrintDocument() {
    var el = document.getElementById("print-area-dx");
    var inner = el ? el.innerHTML : "<p>No diagnostic request on file.</p>";
    return (
        "<html><head><title>MB.EA Diagnostic Request</title>" +
        "<style>@page{size:letter portrait;margin:14mm;}" +
        "body{font-family:Arial,Helvetica,sans-serif;padding:24px;font-size:13px;color:#0f172a;}" +
        ".rx-preview-clinic{font-size:18px;font-weight:800;text-align:center;}" +
        ".rx-preview-addr{font-size:12px;color:#475569;text-align:center;margin-bottom:10px;}" +
        ".rx-preview-stamp{font-size:20px;letter-spacing:1px;font-weight:900;margin:10px 0;}" +
        ".rx-preview-patient-row{display:flex;gap:18px;font-size:13px;margin-bottom:6px;}" +
        ".rx-preview-diag{font-size:13px;margin:8px 0;}" +
        ".rx-preview-notes-label,.rx-preview-meds-label{font-weight:800;font-size:12.5px;margin:10px 0 4px;}" +
        ".rx-preview-meds-list{margin:0;padding-left:20px;}" +
        ".rx-preview-notes-text{font-size:13px;}" +
        ".rx-preview-sig-line{margin-top:36px;}" +
        ".rx-sig-line-bar{height:1px;background:#0f172a;width:200px;margin-bottom:4px;}" +
        ".rx-sig-name{font-weight:700;}.rx-sig-lic{font-size:12px;}" +
        "</style></head><body>" +
        inner +
        "</body></html>"
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

function createLxItemRow(prefill) {
    prefill = prefill || {};
    var row = document.createElement("div");
    row.className = "lx-item-row";

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
    if (event.target.closest(".rx-med-row, .lx-item-row")) updateRxPreview();
});
document.addEventListener("change", function (event) {
    if (event.target.closest(".rx-med-row, .lx-item-row")) updateRxPreview();
});
updateRxPreview();
updateDxPreview();
