/* psychiatrist_prescriptions.js — Rx/Dx subtabs, medication rows, preview, templates, print */

/* ============================================================
     PRESCRIPTIONS — SUB-TABS
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
if (rxDate) rxDate.value = todayStr;
if (dxDate) dxDate.value = todayStr;

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
    });

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
    qtyInput.className = "med-qty-input";
    qtyInput.placeholder = "Qty";
    qtyInput.value = prefill.qty || "";

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
        var patientLabel = patient ? patient.name || "—" : "—";
        var age = document.getElementById("rx-age").value || "—";
        var date = document.getElementById("rx-date").value || todayStr;
        var diag = document.getElementById("rx-diagnosis").value || "—";
        var notes = document.getElementById("rx-notes").value || "—";

        document.getElementById("preview-patient").textContent = patientLabel;
        document.getElementById("preview-age").textContent = age;
        document.getElementById("preview-date").textContent = date;
        document.getElementById("preview-diag").textContent = diag;
        document.getElementById("preview-notes").textContent = notes;

        var medsList = document.getElementById("preview-meds-list");
        medsList.innerHTML = "";
        var medications = [];
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
                (freq.length ? " — " + freq.join(", ") : "") +
                (qty ? " · Qty: " + qty : "");
            medsList.appendChild(li);
        });

        if (!patientId) {
            showToast("Please select a patient first.");
            return;
        }

        apiFetch(
            (ROUTES.prescriptionsStore || "").replace("__ID__", patientId),
            {
                method: "POST",
                body: JSON.stringify({
                    diagnosis: diag,
                    medications: medications,
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
        focus: t.focus || payload.focus || "",
        items: t.items || payload.items || [],
        payload: payload,
    };
}

function upsertTemplateLocal(template) {
    var t = normalizeTemplate(template);
    var list =
        t.type === "dx"
            ? DX_TEMPLATES
            : t.type === "lx"
              ? LX_TEMPLATES
              : RX_TEMPLATES;
    var idx = list.findIndex(function (item) {
        return String(item.id) === String(t.id);
    });
    if (idx >= 0) list[idx] = t;
    else list.push(t);
    if (t.type === "dx") DX_TEMPLATES = list;
    else if (t.type === "lx") LX_TEMPLATES = list;
    else RX_TEMPLATES = list;
}

function removeTemplateLocal(type, id) {
    if (type === "dx") {
        DX_TEMPLATES = DX_TEMPLATES.filter(function (t) {
            return String(t.id) !== String(id);
        });
    } else if (type === "lx") {
        LX_TEMPLATES = LX_TEMPLATES.filter(function (t) {
            return String(t.id) !== String(id);
        });
    } else {
        RX_TEMPLATES = RX_TEMPLATES.filter(function (t) {
            return String(t.id) !== String(id);
        });
    }
}

function buildTemplateItem(t, onApply) {
    var div = document.createElement("div");
    div.className = "template-item";
    div.innerHTML =
        '<div class="template-item-top">' +
        '<span class="template-name">' +
        (t.name || "Template") +
        "</span>" +
        (t.tag
            ? '<span class="template-tag ' +
              (t.tagClass || "") +
              '">' +
              t.tag +
              "</span>"
            : "") +
        "</div>" +
        '<div class="template-desc">' +
        (t.desc || "") +
        "</div>" +
        '<div class="template-actions" style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px;">' +
        '<button type="button" class="btn-outline-sm tpl-apply">+ Apply</button>' +
        '<button type="button" class="btn-outline-sm tpl-edit">Edit</button>' +
        '<button type="button" class="btn-outline-sm tpl-delete" style="color:#b91c1c;">Delete</button>' +
        "</div>";
    div.querySelector(".tpl-apply").addEventListener("click", function () {
        onApply(t);
    });
    div.querySelector(".tpl-edit").addEventListener("click", function () {
        openTemplateModal(t.type, t);
    });
    div.querySelector(".tpl-delete").addEventListener("click", function () {
        deleteTemplate(t);
    });
    return div;
}

function buildRxTemplates() {
    var list = document.getElementById("rx-template-list");
    if (!list) return;
    list.innerHTML = "";
    if (!RX_TEMPLATES.length) {
        list.innerHTML =
            '<div class="template-item" style="color:#64748b;">No Rx templates yet. Click Manage / Add.</div>';
        return;
    }
    RX_TEMPLATES.map(normalizeTemplate).forEach(function (t) {
        list.appendChild(buildTemplateItem(t, applyRxTemplate));
    });
}

function buildDxTemplates() {
    var list = document.getElementById("dx-template-list");
    if (!list) return;
    list.innerHTML = "";
    if (!DX_TEMPLATES.length) {
        list.innerHTML =
            '<div class="template-item" style="color:#64748b;">No diagnostic templates yet. Click Manage / Add.</div>';
        return;
    }
    DX_TEMPLATES.map(normalizeTemplate).forEach(function (t) {
        list.appendChild(buildTemplateItem(t, applyDxTemplate));
    });
}

function buildLxTemplates() {
    var list = document.getElementById("lx-template-list");
    if (!list) return;
    list.innerHTML = "";
    if (!LX_TEMPLATES.length) {
        list.innerHTML =
            '<div class="template-item" style="color:#64748b;">No lifestyle templates yet. Click Manage / Add.</div>';
        return;
    }
    LX_TEMPLATES.map(normalizeTemplate).forEach(function (t) {
        list.appendChild(buildTemplateItem(t, applyLxTemplate));
    });
}

function applyRxTemplate(t) {
    if (!rxMedsList) return;
    t = normalizeTemplate(t);
    rxMedsList.innerHTML = "";
    (t.meds || []).forEach(function (m) {
        rxMedsList.appendChild(createMedRow(m));
    });
    if (rxDiagInput) rxDiagInput.value = t.diag || "";
    ri();
    showToast('Template "' + t.name + '" applied.');
}

function applyDxTemplate(t) {
    t = normalizeTemplate(t);
    qsa(".dx-cb").forEach(function (cb) {
        cb.checked = false;
    });
    (t.tests || []).forEach(function (test) {
        var cb = qs('.dx-cb[data-test="' + test + '"]');
        if (cb) cb.checked = true;
    });
    showToast('Template "' + t.name + '" applied.');
}

function applyLxTemplate(t) {
    t = normalizeTemplate(t);
    if (lxItemsList) {
        lxItemsList.innerHTML = "";
        (t.items || []).forEach(function (item) {
            lxItemsList.appendChild(createLxItemRow(item));
        });
        if (!(t.items || []).length) lxItemsList.appendChild(createLxItemRow());
        if (addLxItemBtn) addLxItemBtn.disabled = false;
    }
    var focusEl = document.getElementById("lx-focus");
    if (focusEl) focusEl.value = t.focus || "";
    ri();
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
    setVal("tpl-focus", template ? template.focus || "" : "");
    setVal("tpl-lx-items", template ? lxItemsToText(template.items) : "");

    var title = document.getElementById("template-modal-title");
    if (title)
        title.textContent =
            (template ? "Edit" : "Add") +
            (type === "dx"
                ? " Diagnostic Template"
                : type === "lx"
                  ? " Lifestyle Template"
                  : " Rx Template");

    var diagWrap = document.getElementById("tpl-diag-wrap");
    var medsWrap = document.getElementById("tpl-meds-wrap");
    var testsWrap = document.getElementById("tpl-tests-wrap");
    var lxWrap = document.getElementById("tpl-lx-wrap");
    if (type === "dx") {
        if (diagWrap) hide(diagWrap);
        if (medsWrap) hide(medsWrap);
        if (testsWrap) show(testsWrap);
        if (lxWrap) hide(lxWrap);
    } else if (type === "lx") {
        if (diagWrap) hide(diagWrap);
        if (medsWrap) hide(medsWrap);
        if (testsWrap) hide(testsWrap);
        if (lxWrap) show(lxWrap);
    } else {
        if (diagWrap) show(diagWrap);
        if (medsWrap) show(medsWrap);
        if (testsWrap) hide(testsWrap);
        if (lxWrap) hide(lxWrap);
    }
    openModal("template-modal");
}

function deleteTemplate(t) {
    if (!t.id) return;
    if (!window.confirm('Delete template "' + t.name + '"?')) return;
    var base =
        (window.PSYCH_ROUTES || {}).templatesUpdate ||
        "/psychiatrist/clinical-templates";
    apiFetch(base + "/" + t.id, { method: "DELETE" })
        .then(function (data) {
            removeTemplateLocal(t.type, t.id);
            buildRxTemplates();
            buildDxTemplates();
            buildLxTemplates();
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
        } else if (type === "lx") {
            payload.focus = getVal("tpl-focus").trim() || null;
            payload.items = parseLxItemsText(getVal("tpl-lx-items"));
            if (!payload.items.length) {
                showToast(
                    "Add at least one lifestyle item (one per line: Category | Title | Target | Frequency | Duration | Instructions)."
                );
                return;
            }
        } else {
            payload.diag = getVal("tpl-diag").trim() || null;
            payload.meds = parseMedsText(getVal("tpl-meds"));
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
                buildLxTemplates();
                closeModal("template-modal");
                showToast(data.message || "Template saved.");
            })
            .catch(function (err) {
                showToast(err.message);
            });
    });
}

/* ============================================================
     RX: PRINT — formal Rx pad (matches reference layout)
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
        if (!name) return;
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
        ".foot{margin-top:14px;font-size:10px;color:#333;display:flex;justify-content:space-between;}" +
        "</style>"
    );
}

function buildRxMedRowsHtml(medList) {
    medList = medList || [];
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

    var emptyRows = Math.max(0, 8 - medList.length);
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
) {
    var logoTag = P.clinicLogo
        ? '<img src="' +
          P.clinicLogo +
          '" style="width:64px;height:64px;object-fit:contain;border-radius:8px;" />'
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
        '<div class="contact-line" style="text-align:center;">Contact <u>0905.071.3671 (Rose, secretary)</u> for appointments & inquiries</div>' +
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
        '<table class="rx"><thead><tr>' +
        '<th rowspan="2" class="meds-head"><div class="meds-head-inner"><span class="rx-stamp-sm">&#8477;</span><span class="meds-title">MEDICATIONS</span></div></th>' +
        '<th class="grp" colspan="2">Breakfast</th><th class="grp" colspan="2">Lunch</th><th class="grp" colspan="2">Dinner</th>' +
        '<th rowspan="2" class="vth"><span class="vtext vtext-main">BEDTIME</span></th>' +
        '<th rowspan="2" class="vth"><span class="vtext vtext-main">QUANTITY</span></th></tr>' +
        "<tr>" +
        '<th class="vth"><span class="vtext vtext-sub">Before</span></th><th class="vth"><span class="vtext vtext-sub">After</span></th>' +
        '<th class="vth"><span class="vtext vtext-sub">Before</span></th><th class="vth"><span class="vtext vtext-sub">After</span></th>' +
        '<th class="vth"><span class="vtext vtext-sub">Before</span></th><th class="vth"><span class="vtext vtext-sub">After</span></th>' +
        "</tr></thead><tbody>" +
        buildRxMedRowsHtml(medList) +
        "</tbody></table>" +
        '<div class="sig-block"><div class="sig-bar"></div>' +
        '<div class="sig-name">' +
        escHtml(doc.name || "\u2014") +
        "</div>" +
        '<div class="sig-lic">License No. ' +
        escHtml(doc.license_no || "\u2014") +
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

    if (!patientId) {
        showToast("Please select a patient first.");
        return;
    }
    if (!medList.length) {
        showToast("Please add at least one medication.");
        return;
    }

    var patientName = patient
        ? patient.name || "—"
        : document.getElementById("rx-patient-search").value || "—";
    var patientSex = patient && patient.sex ? patient.sex : "";
    var age = document.getElementById("rx-age").value || "";
    var date = document.getElementById("rx-date").value || todayStr;
    var address = patient && patient.address ? patient.address : "";

    showPrintPreview(
        "Print Preview — Prescription",
        buildRxPadDocument(
            P,
            doc,
            patientName,
            age,
            date,
            address,
            patientSex,
            medList,
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
        var cat = document.createElement("div");
        cat.className = "dx-cat-label";
        cat.textContent = g.cat;
        list.appendChild(cat);
        g.tests.forEach(function (test) {
            var lbl = document.createElement("label");
            lbl.className = "dx-check-item";
            var cb = document.createElement("input");
            cb.type = "checkbox";
            cb.className = "dx-cb";
            cb.setAttribute("data-test", test);
            lbl.appendChild(cb);
            lbl.appendChild(document.createTextNode(test));
            list.appendChild(lbl);
        });
    });
}

var generateDxBtn = document.getElementById("generate-dx-btn");
if (generateDxBtn) {
    generateDxBtn.addEventListener("click", function () {
        var patientId = document.getElementById("dx-patient").value;
        var patient = findPatientLocal(patientId);
        var patientLabelText = patient
            ? patient.name || "—"
            : document.getElementById("dx-patient-search").value || "—";
        var date = document.getElementById("dx-date").value || todayStr;
        var notes = document.getElementById("dx-notes").value || "—";

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
        "Print Preview — Diagnostic Request",
        buildDxPrintDocument(),
    );
};

/* ============================================================
     LIFESTYLE PRESCRIPTION (Lx)
   ============================================================ */
var LX_TEMPLATES = Array.isArray((window.PSYCH_DATA || {}).lxTemplates)
    ? window.PSYCH_DATA.lxTemplates.slice()
    : [];

var lxItemsList = document.getElementById("lx-items-list");
var addLxItemBtn = document.getElementById("add-lx-item-btn");
var lxDate = document.getElementById("lx-date");
if (lxDate) lxDate.value = todayStr;

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

    // Delete
    var delBtn = document.createElement("button");
    delBtn.type = "button";
    delBtn.className = "med-del-btn";
    delBtn.innerHTML = '<i data-feather="trash-2"></i>';
    delBtn.addEventListener("click", function () {
        if (!lxItemsList) return;
        if (lxItemsList.children.length > 1) {
            row.remove();
        } else {
            title.value = "";
            target.value = "";
            freq.value = "";
            dur.value = "";
            instr.value = "";
        }
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

if (lxItemsList) {
    if (!lxItemsList.children.length) lxItemsList.appendChild(createLxItemRow());
    if (addLxItemBtn) {
        addLxItemBtn.addEventListener("click", function () {
            lxItemsList.appendChild(createLxItemRow());
        });
    }
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

/* ---- Lx: patient selection + current baseline strip ---- */
function renderLxBaseline(ls) {
    var strip = document.getElementById("lx-baseline");
    if (!strip) return;
    if (!ls) {
        setVal("lx-base-sleep", "—");
        setVal("lx-base-exercise", "No assessment on file");
        setVal("lx-base-nutrition", "—");
        setVal("lx-base-motivation", "—");
    } else {
        setVal(
            "lx-base-sleep",
            ls.sleep_hours != null ? ls.sleep_hours + " h/night" : "—"
        );
        setVal("lx-base-exercise", ls.exercise_frequency || "—");
        setVal("lx-base-nutrition", ls.fruits_veg_servings || "—");
        setVal("lx-base-motivation", ls.motivation_level || "—");
    }
    show(strip);
    ri();
}

var lxPatientPicker = createPatientPicker({
    searchId: "lx-patient-search",
    hiddenId: "lx-patient",
    dropdownId: "lx-patient-dropdown",
    ageId: "lx-age",
    onSelect: function (patient) {
        if (!patient || !patient.id) return;
        if (patient.lifestyle_assessment) {
            renderLxBaseline(patient.lifestyle_assessment);
            return;
        }
        var base =
            (window.PSYCH_ROUTES || {}).patientsShow ||
            "/psychiatrist/patients";
        apiFetch(base + "/" + patient.id)
            .then(function (data) {
                renderLxBaseline(
                    data.patient ? data.patient.lifestyle_assessment : null
                );
            })
            .catch(function () {
                renderLxBaseline(null);
            });
    },
});

/* ---- Lx: preview grouped by category ---- */
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
                .join(" · ");
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
    return html || '<div class="lx-prev-item-meta">—</div>';
}

var generateLxBtn = document.getElementById("generate-lx-btn");
if (generateLxBtn) {
    generateLxBtn.addEventListener("click", function () {
        var patientId = document.getElementById("lx-patient").value;
        var patient = findPatientLocal(patientId);
        var items = collectLxItems();
        var focus = document.getElementById("lx-focus").value.trim();
        var notes = document.getElementById("lx-notes").value.trim();
        var followUp = document.getElementById("lx-follow-up").value;

        if (!patientId) {
            showToast("Please select a patient first.");
            return;
        }
        if (!items.length) {
            showToast("Add at least one lifestyle item.");
            return;
        }

        var patientLabelText = patient
            ? patient.name || "—"
            : document.getElementById("lx-patient-search").value || "—";
        var date = document.getElementById("lx-date").value || todayStr;

        document.getElementById("lx-prev-patient").textContent =
            patientLabelText;
        document.getElementById("lx-prev-age").textContent =
            document.getElementById("lx-age").value || "—";
        document.getElementById("lx-prev-date").textContent = date;
        document.getElementById("lx-prev-focus").textContent = focus || "—";
        document.getElementById("lx-prev-items").innerHTML =
            buildLxGroupedHtml(items);
        document.getElementById("lx-prev-notes").textContent = notes || "—";
        document.getElementById("lx-prev-followup").innerHTML = followUp
            ? "<strong>Follow-up:</strong> " + escHtml(followUp)
            : "";

        apiFetch(
            ((window.PSYCH_ROUTES || {}).lifestylePrescriptionsStore ||
                "/psychiatrist/patients").replace("__ID__", patientId),
            {
                method: "POST",
                body: JSON.stringify({
                    focus: focus,
                    items: items,
                    notes: notes,
                    status: "Draft",
                    follow_up_date: followUp || null,
                }),
            }
        )
            .then(function (data) {
                showToast(data.message || "Lifestyle prescription saved.");
            })
            .catch(function (err) {
                showToast(err.message);
            });
    });
}

/* ---- Lx: print ---- */
function buildLxPrintDocument() {
    var el = document.getElementById("print-area-lx");
    var inner = el
        ? el.innerHTML
        : "<p>No lifestyle prescription on file.</p>";
    return (
        "<html><head><title>MB.EA Lifestyle Prescription</title>" +
        "<style>@page{size:letter portrait;margin:14mm;}" +
        "body{font-family:Arial,Helvetica,sans-serif;padding:24px;font-size:13px;color:#0f172a;}" +
        ".rx-preview-clinic{font-size:18px;font-weight:800;text-align:center;}" +
        ".rx-preview-addr{font-size:12px;color:#475569;text-align:center;margin-bottom:10px;}" +
        ".rx-preview-stamp{font-size:20px;letter-spacing:1px;font-weight:900;margin:10px 0;}" +
        ".rx-preview-patient-row{display:flex;gap:18px;font-size:13px;margin-bottom:6px;}" +
        ".rx-preview-diag{font-size:13px;margin:8px 0;}" +
        ".rx-preview-notes-label,.rx-preview-meds-label{font-weight:800;font-size:12.5px;margin:10px 0 4px;}" +
        ".rx-preview-notes-text{font-size:13px;}" +
        ".lx-prev-group-label{font-weight:800;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;color:#475569;margin:10px 0 3px;}" +
        ".lx-prev-item{padding:3px 0 3px 12px;border-left:2px solid #cbd5e1;margin-bottom:6px;}" +
        ".lx-prev-item-title{font-weight:700;}" +
        ".lx-prev-item-meta{font-size:11.5px;color:#64748b;}" +
        ".lx-prev-item-instr{font-size:12px;color:#334155;}" +
        ".lx-prev-followup{font-size:12.5px;color:#334155;margin-top:8px;}" +
        ".rx-preview-sig-line{margin-top:36px;}" +
        ".rx-sig-line-bar{height:1px;background:#0f172a;width:200px;margin-bottom:4px;}" +
        ".rx-sig-name{font-weight:700;}.rx-sig-lic{font-size:12px;}" +
        "</style></head><body>" +
        inner +
        "</body></html>"
    );
}

window.printLx = function () {
    var patientId = document.getElementById("lx-patient").value;
    if (!patientId || !collectLxItems().length) {
        showToast("Select a patient and generate the plan before printing.");
        return;
    }
    showPrintPreview(
        "Print Preview — Lifestyle Prescription",
        buildLxPrintDocument(),
    );
};

buildLxTemplates();
