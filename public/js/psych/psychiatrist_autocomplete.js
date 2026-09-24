/* Shared fuzzy autocomplete for psychiatrist prescription and template fields. */
(function () {
    "use strict";

    var recentKey = "mbea-psychiatrist-autocomplete-recent";
    var active = null;
    var sequence = 0;
    var presets = {
        medications: [
            ["Sertraline", "SSRI", "Zoloft|Lustral"],
            ["Fluoxetine", "SSRI", "Prozac"],
            ["Escitalopram", "SSRI", "Lexapro"],
            ["Paroxetine", "SSRI", "Paxil"],
            ["Venlafaxine", "SNRI", "Effexor"],
            ["Duloxetine", "SNRI", "Cymbalta"],
            ["Bupropion", "NDRI", "Wellbutrin"],
            ["Mirtazapine", "NaSSA", "Remeron"],
            ["Quetiapine", "Atypical antipsychotic", "Seroquel"],
            ["Olanzapine", "Atypical antipsychotic", "Zyprexa"],
            ["Risperidone", "Atypical antipsychotic", "Risperdal"],
            ["Aripiprazole", "Atypical antipsychotic", "Abilify"],
            ["Haloperidol", "Typical antipsychotic", "Haldol"],
            ["Clonazepam", "Benzodiazepine", "Klonopin"],
            ["Alprazolam", "Benzodiazepine", "Xanax"],
            ["Lorazepam", "Benzodiazepine", "Ativan"],
            ["Diazepam", "Benzodiazepine", "Valium"],
            ["Zolpidem", "Hypnotic", "Ambien"],
            ["Melatonin", "Sleep aid", ""],
            ["Lithium", "Mood stabilizer", ""],
            ["Valproic Acid", "Mood stabilizer", "Depakene"],
            ["Carbamazepine", "Mood stabilizer", "Tegretol"],
            ["Lamotrigine", "Mood stabilizer", "Lamictal"],
            ["Methylphenidate", "Stimulant", "Ritalin"],
            ["Atomoxetine", "SNRI", "Strattera"],
            ["Clozapine", "Atypical antipsychotic", "Clozaril"],
            ["Paliperidone", "Atypical antipsychotic", "Invega"],
            ["Lurasidone", "Atypical antipsychotic", "Latuda"],
            ["Trazodone", "SARI", "Desyrel"],
            ["Biperiden", "Anticholinergic", "Akineton"],
        ].map(function (item) {
            return {
                value: item[0],
                label: item[0],
                secondary: item[1],
                aliases: item[2],
            };
        }),
        diagnoses: [
            [
                "Major Depressive Disorder F32.x",
                "ICD-10 F32.x",
                "MDD|depression|major depression",
            ],
            [
                "Generalized Anxiety Disorder F41.1",
                "ICD-10 F41.1",
                "GAD|anxiety",
            ],
            [
                "Post-traumatic Stress Disorder F43.1",
                "ICD-10 F43.1",
                "PTSD|trauma",
            ],
            ["Obsessive-compulsive Disorder F42", "ICD-10 F42", "OCD"],
            [
                "Attention-deficit Hyperactivity Disorder F90.0",
                "ICD-10 F90.0",
                "ADHD|attention deficit",
            ],
            ["Bipolar I Disorder F31.9", "ICD-10 F31.9", "bipolar 1"],
            ["Bipolar II Disorder F31.81", "ICD-10 F31.81", "bipolar 2"],
            ["Schizophrenia F20.9", "ICD-10 F20.9", "psychosis"],
            ["Insomnia Disorder F51.01", "ICD-10 F51.01", "insomnia|sleep"],
            ["Panic Disorder F41.0", "ICD-10 F41.0", "panic"],
            [
                "Borderline Personality Disorder F60.3",
                "ICD-10 F60.3",
                "BPD|borderline",
            ],
            [
                "Persistent Depressive Disorder F34.1",
                "ICD-10 F34.1",
                "dysthymia",
            ],
        ].map(function (item) {
            return {
                value: item[0],
                label: item[0],
                secondary: item[1],
                aliases: item[2],
            };
        }),
        timing: [
            "PRN",
            "Once daily",
            "Twice daily",
            "Three times daily",
            "Every other day",
            "Weekly",
            "With food",
            "Before meals",
            "At night",
            "Morning",
            "Breakfast",
            "Lunch",
            "Dinner",
            "Bedtime",
        ].map(function (value) {
            return {
                value: value,
                label: value,
                secondary: "Frequency / timing",
            };
        }),
        quantities: [7, 14, 30, 60, 90].map(function (value) {
            return {
                value: String(value),
                label: String(value) + " tabs",
                secondary: "Quantity",
            };
        }),
        strengths: [
            "5mg",
            "10mg",
            "25mg",
            "50mg",
            "100mg",
            "150mg",
            "200mg",
            "300mg",
            "500mg",
        ].map(function (value) {
            return { value: value, label: value, secondary: "Common strength" };
        }),
        categories: [
            "Sleep",
            "Stress / Mind",
            "Exercise",
            "Nutrition",
            "Social",
            "Other",
        ].map(function (value) {
            return {
                value: value,
                label: value,
                secondary: "Lifestyle category",
            };
        }),
        interventions: [
            [
                "Fixed wake-up time",
                "Sleep",
                "7-8 hours sleep | Daily | 4 weeks",
            ],
            [
                "Morning walk",
                "Exercise",
                "150 min/week | 5x per week | 4 weeks",
            ],
            [
                "Sleep restriction plan",
                "Sleep",
                "Consistent sleep window | Daily | 4 weeks",
            ],
            [
                "Breathing practice",
                "Stress / Mind",
                "5 minutes | Daily | 2 weeks",
            ],
            [
                "Mindfulness practice",
                "Stress / Mind",
                "10 minutes | Daily | 4 weeks",
            ],
            [
                "Balanced plate plan",
                "Nutrition",
                "Three balanced meals | Daily | 4 weeks",
            ],
            [
                "Reduce caffeine",
                "Nutrition",
                "No caffeine after lunch | Daily | 2 weeks",
            ],
        ].map(function (item) {
            return {
                value: item[0],
                label: item[0],
                secondary: item[1],
                aliases: item[2],
            };
        }),
        durations: ["1 week", "2 weeks", "4 weeks", "3 months", "6 months"].map(
            function (value) {
                return { value: value, label: value, secondary: "Duration" };
            },
        ),
        phrases: [
            "Follow-up in 2 weeks",
            "Take with food",
            "Avoid alcohol",
            "Do not stop abruptly",
            "Return sooner for worsening symptoms",
            "Continue medication as prescribed",
            "Maintain a regular sleep schedule",
            "Seek urgent care for safety concerns",
        ].map(function (value) {
            return { value: value, label: value, secondary: "Clinical phrase" };
        }),
        tests: [
            ["CBC with differential", "Hematology", "cbc|complete blood count"],
            ["TSH", "Thyroid function", "thy|thyroid"],
            ["Free T3", "Thyroid function", "thy|thyroid"],
            ["Free T4", "Thyroid function", "thy|thyroid"],
            ["Liver function panel", "Chemistry", "lft|liver"],
            ["Fasting Blood Sugar", "Chemistry", "fbs|glucose"],
            ["Serum creatinine", "Renal function", "creatinine|kidney"],
            ["Lipid profile", "Metabolic", "lipids|cholesterol"],
            ["Urinalysis", "Urine", "ua"],
        ].map(function (item) {
            return {
                value: item[0],
                label: item[0],
                secondary: item[1],
                aliases: item[2],
            };
        }),
    };

    function normalize(value) {
        return String(value || "")
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase()
            .replace(/\s+/g, " ")
            .trim();
    }
    function readRecent() {
        try {
            return JSON.parse(localStorage.getItem(recentKey) || "{}");
        } catch (error) {
            return {};
        }
    }
    function remember(item) {
        try {
            var recent = readRecent();
            recent[normalize(item.value)] = Date.now();
            localStorage.setItem(recentKey, JSON.stringify(recent));
        } catch (error) {}
    }
    function editDistance(a, b) {
        var row = Array.from({ length: b.length + 1 }, function (_, index) {
            return index;
        });
        for (var i = 1; i <= a.length; i += 1) {
            var next = [i];
            for (var j = 1; j <= b.length; j += 1)
                next[j] = Math.min(
                    next[j - 1] + 1,
                    row[j] + 1,
                    row[j - 1] + (a[i - 1] === b[j - 1] ? 0 : 1),
                );
            row.splice(0, row.length);
            Array.prototype.push.apply(row, next);
        }
        return row[b.length];
    }
    function score(query, item) {
        var q = normalize(query);
        if (!q) return 0;
        var fields = [
            item.value,
            item.label,
            item.secondary,
            item.aliases || "",
        ]
            .map(normalize)
            .filter(Boolean);
        var best = 0;
        fields.forEach(function (field) {
            var words = field.split(" ");
            if (field === q) best = Math.max(best, 1000);
            else if (field.indexOf(q) === 0) best = Math.max(best, 800);
            else if (
                words.some(function (word) {
                    return word.indexOf(q) === 0;
                })
            )
                best = Math.max(best, 700);
            else if (field.indexOf(q) >= 0) best = Math.max(best, 600);
            else {
                var pos = 0;
                for (var k = 0; k < q.length; k += 1) {
                    pos = field.indexOf(q[k], pos);
                    if (pos < 0) break;
                    pos += 1;
                }
                if (pos >= 0 && q.length > 1) best = Math.max(best, 400);
                else {
                    var distance = editDistance(
                        q,
                        field.slice(0, Math.max(q.length + 2, 8)),
                    );
                    var allowed = q.length >= 7 ? 2 : q.length >= 4 ? 1 : 0;
                    if (distance <= allowed)
                        best = Math.max(best, 300 - distance * 20);
                }
            }
        });
        return best;
    }
    function ranked(query, source, limit) {
        var recent = readRecent();
        return source
            .map(function (item) {
                return {
                    item: item,
                    score: score(query, item),
                    recent: recent[normalize(item.value)] || 0,
                };
            })
            .filter(function (entry) {
                return entry.score > 0 || !normalize(query);
            })
            .sort(function (a, b) {
                return (
                    b.score - a.score ||
                    b.recent - a.recent ||
                    a.item.label.localeCompare(b.item.label)
                );
            })
            .slice(0, limit || 8)
            .map(function (entry) {
                return entry.item;
            });
    }
    function highlight(text, query) {
        var normalized = normalize(query);
        if (!normalized) return escapeHtml(text);
        var index = normalize(text).indexOf(normalized);
        if (index < 0) return escapeHtml(text);
        return (
            escapeHtml(text.slice(0, index)) +
            "<mark>" +
            escapeHtml(text.slice(index, index + normalized.length)) +
            "</mark>" +
            escapeHtml(text.slice(index + normalized.length))
        );
    }
    function escapeHtml(value) {
        return String(value || "").replace(/[&<>"']/g, function (char) {
            return {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#39;",
            }[char];
        });
    }
    function completeMatch(query, source) {
        var value = normalize(query);
        if (!value) return false;
        return source.some(function (item) {
            return normalize(item.label || item.value) === value;
        });
    }
    function close(instance) {
        if (!instance) return;
        instance.box.remove();
        if (active === instance) active = null;
        instance.input.removeAttribute("aria-activedescendant");
        instance.input.setAttribute("aria-expanded", "false");
        instance.input.setAttribute("aria-live", "polite");
    }
    function show(input, source, onPick, query, force) {
        var typed = query == null ? input.value : query;
        if (!force && completeMatch(typed, source)) {
            input.setAttribute("aria-expanded", "false");
            return;
        }
        if (active) close(active);
        var results = ranked(typed, source, 8);
        var box = document.createElement("div");
        box.className = "ac-dropdown";
        box.id = "ac-listbox-" + ++sequence;
        box.setAttribute("role", "listbox");
        var instance = {
            input: input,
            box: box,
            results: results,
            index: -1,
            onPick: onPick,
        };
        active = instance;
        results.forEach(function (item, index) {
            var option = document.createElement("div");
            option.className = "ac-option";
            option.id = "ac-option-" + ++sequence;
            option.setAttribute("role", "option");
            option.innerHTML =
                "<span>" +
                highlight(item.label, typed) +
                "</span><small>" +
                escapeHtml(item.secondary || "") +
                "</small>";
            option.addEventListener("mousedown", function (event) {
                event.preventDefault();
                pick(instance, index);
            });
            box.appendChild(option);
        });
        var count = document.createElement("div");
        count.className = "sr-only";
        count.setAttribute("aria-live", "polite");
        count.textContent = results.length + " results";
        box.appendChild(count);
        if (!results.length) {
            var empty = document.createElement("div");
            empty.className = "ac-empty";
            empty.textContent = query
                ? "Use '" + query + "'"
                : "No suggestions";
            box.appendChild(empty);
        }
        document.body.appendChild(box);
        position(instance);
        input.setAttribute("role", "combobox");
        input.setAttribute("aria-expanded", "true");
        input.setAttribute("aria-controls", box.id || "ac-listbox");
        input.setAttribute("aria-live", "polite");
        if (results.length)
            input.setAttribute("aria-activedescendant", box.children[0].id);
    }
    function position(instance) {
        var rect = instance.input.getBoundingClientRect();
        var width = Math.max(rect.width, 240);
        instance.box.style.left =
            Math.max(8, Math.min(rect.left, window.innerWidth - width - 8)) +
            "px";
        instance.box.style.width =
            Math.min(width, window.innerWidth - 16) + "px";
        var below = window.innerHeight - rect.bottom;
        instance.box.style.top =
            (below < 230 && rect.top > 230
                ? rect.top - Math.min(230, instance.box.offsetHeight)
                : rect.bottom) + "px";
    }
    function pick(instance, index) {
        var item = instance.results[index];
        if (!item) return;
        instance.input.value = item.value;
        instance.input.dataset.acSelected = "true";
        remember(item);
        if (instance.onPick) instance.onPick(item);
        instance.input.dispatchEvent(new Event("input", { bubbles: true }));
        close(instance);
    }
    function bind(input, source, onPick) {
        if (!input || input.dataset.acBound) return;
        input.dataset.acBound = "true";
        var timer;
        input.addEventListener("focus", function () {
            if (!input.value.trim()) show(input, source, onPick, "");
        });
        input.addEventListener("input", function () {
            if (input.dataset.acSelected === "true") {
                delete input.dataset.acSelected;
                clearTimeout(timer);
                return;
            }
            clearTimeout(timer);
            timer = setTimeout(function () {
                if (!completeMatch(input.value, source))
                    show(input, source, onPick, input.value);
                else close(active);
            }, 80);
        });
        input.addEventListener("keydown", function (event) {
            if (
                (event.key === "ArrowDown" ||
                    (event.key === "Alt" && event.key === "ArrowDown")) &&
                !active
            ) {
                event.preventDefault();
                show(input, source, onPick, input.value, true);
                return;
            }
            if (!active || active.input !== input) return;
            var options = active.box.querySelectorAll(".ac-option");
            if (event.key === "ArrowDown" || event.key === "ArrowUp") {
                event.preventDefault();
                active.index =
                    (active.index +
                        (event.key === "ArrowDown" ? 1 : options.length - 1)) %
                    options.length;
                options.forEach(function (option, index) {
                    option.classList.toggle("active", index === active.index);
                });
                if (options[active.index])
                    input.setAttribute(
                        "aria-activedescendant",
                        options[active.index].id,
                    );
            } else if (event.key === "Home") {
                event.preventDefault();
                active.index = 0;
            } else if (event.key === "End") {
                event.preventDefault();
                active.index = options.length - 1;
            } else if (
                (event.key === "Enter" || event.key === "Tab") &&
                active.index >= 0
            ) {
                event.preventDefault();
                pick(active, active.index);
            } else if (event.key === "Escape") close(active);
        });
        input.addEventListener("blur", function () {
            setTimeout(function () {
                if (
                    active &&
                    active.input === input &&
                    !active.box.matches(":hover")
                )
                    close(active);
            }, 140);
        });
    }
    function attachAll(root) {
        var scope = root || document;
        var libraryTemplates = (
            (window.PSYCH_DATA || {}).manageTemplates || []
        ).map(function (template) {
            return {
                value: template.name || "",
                label: template.name || "",
                secondary: [
                    template.tag,
                    template.diag,
                    (template.meds || [])
                        .map(function (med) {
                            return med.name;
                        })
                        .join(", "),
                ]
                    .filter(Boolean)
                    .join(" · "),
                aliases: template.tag || "",
            };
        });
        scope.querySelectorAll(".template-search").forEach(function (input) {
            bind(input, libraryTemplates);
        });
        scope
            .querySelectorAll("#rx-diagnosis,#tpl-diag")
            .forEach(function (input) {
                bind(input, presets.diagnoses);
            });
        scope
            .querySelectorAll(".med-name-wrap input, #tpl-meds")
            .forEach(function (input) {
                bind(input, presets.medications);
            });
        scope.querySelectorAll(".freq-custom-input").forEach(function (input) {
            bind(input, presets.timing);
        });
        scope.querySelectorAll(".med-qty-input").forEach(function (input) {
            bind(input, presets.quantities);
        });
        scope.querySelectorAll(".rx-med-row select").forEach(function (input) {
            bind(input, presets.strengths);
        });
        scope.querySelectorAll(".lx-item-row input").forEach(function (input) {
            var source =
                input.placeholder.indexOf("Intervention") >= 0
                    ? presets.interventions
                    : input.placeholder.indexOf("Duration") >= 0
                      ? presets.durations
                      : presets.phrases;
            bind(input, source);
        });
        scope
            .querySelectorAll("#rx-notes,#dx-notes,#tpl-desc,#tpl-lx-items")
            .forEach(function (input) {
                bind(input, presets.phrases);
            });
        scope.querySelectorAll("#tpl-tag").forEach(function (input) {
            var tags = ((window.PSYCH_DATA || {}).manageTemplates || [])
                .map(function (item) {
                    return item.tag;
                })
                .filter(Boolean)
                .map(function (value) {
                    return {
                        value: value,
                        label: value,
                        secondary: "Existing tag",
                    };
                });
            bind(input, tags);
        });
        scope
            .querySelectorAll("#dx-test-search,#tpl-tests")
            .forEach(function (input) {
                bind(input, presets.tests, function (item) {
                    var checkbox = document.querySelector(
                        '.dx-cb[data-test="' +
                            item.value.replace(/"/g, '\\"') +
                            '"]',
                    );
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.dispatchEvent(
                            new Event("change", { bubbles: true }),
                        );
                        checkbox
                            .closest("label")
                            .scrollIntoView({ block: "nearest" });
                    }
                });
            });
    }
    function bindPatients() {
        ["rx", "dx"].forEach(function (type) {
            var input = document.getElementById(type + "-patient-search");
            if (!input || input.dataset.acPatientBound) return;
            input.dataset.acPatientBound = "true";
            var timer;
            var controller;
            input.addEventListener("input", function () {
                if (input.dataset.acSelected === "true") {
                    delete input.dataset.acSelected;
                    return;
                }
                clearTimeout(timer);
                if (controller) controller.abort();
                var query = input.value.trim();
                if (!query) return;
                timer = setTimeout(function () {
                    controller = new AbortController();
                    fetch(
                        "/psychiatrist/patients/search?q=" +
                            encodeURIComponent(query) +
                            "&limit=8",
                        {
                            headers: { Accept: "application/json" },
                            signal: controller.signal,
                        },
                    )
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (data) {
                            show(
                                input,
                                (data.patients || []).map(function (patient) {
                                    return {
                                        value: patient.name,
                                        label: patient.name,
                                        secondary:
                                            (patient.patient_id || patient.id) +
                                            " · Age " +
                                            (patient.age || "—"),
                                        patient: patient,
                                    };
                                }),
                                function (item) {
                                    var patient = item.patient || {};
                                    var hidden = document.getElementById(
                                        type + "-patient",
                                    );
                                    var age = document.getElementById(
                                        type + "-age",
                                    );
                                    if (hidden) hidden.value = patient.id || "";
                                    if (age && patient.age != null)
                                        age.value = patient.age;
                                },
                            );
                        })
                        .catch(function () {});
                }, 80);
            });
        });
    }
    attachAll(document);
    bindPatients();
    var observer = new MutationObserver(function (records) {
        records.forEach(function (record) {
            record.addedNodes.forEach(function (node) {
                if (node.nodeType === 1) attachAll(node);
            });
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });
    document.addEventListener("click", function (event) {
        if (
            active &&
            !active.box.contains(event.target) &&
            event.target !== active.input
        )
            close(active);
    });
    window.MBEA_AUTOCOMPLETE = { presets: presets, search: ranked };
})();
