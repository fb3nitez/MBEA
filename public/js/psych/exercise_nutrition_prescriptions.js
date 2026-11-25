(function () {
    "use strict";

    var exercise = {
        type: "exercise",
        title: "Exercise Prescription",
        sections: [
            { id: "about", title: "About You", fields: [
                { key: "name", label: "Name", kind: "patient-name", required: true },
                { key: "age", label: "Age", kind: "age", required: true },
                { key: "sex", label: "Sex", kind: "sex", required: true },
                { key: "hwb", label: "Height / Weight / BMI" },
                { key: "vitals", label: "Resting heart rate / Blood pressure" },
                { key: "activity", label: "Current activity level", kind: "radio", required: true, options: [
                    "Mostly sitting", "Light (walks sometimes)", "Active (exercise 1 to 2 days/week)", "Very active"
                ] },
                { key: "meds", label: "Current medicines", kind: "area" }
            ] },
            { id: "safety", title: "Safety Check", fields: [
                { kind: "note", text: "To be completed with your doctor. Have you ever had or do you now have:" },
                { key: "safety", label: "Safety questions", kind: "yesno", required: true, rows: [
                    "Chest pain, pressure or tightness with activity", "Fainting or severe dizziness",
                    "Heart disease, stroke, or a heart condition", "Uncontrolled high blood pressure or diabetes",
                    "Bone, joint or back problems that limit movement", "Pregnancy or recent surgery",
                    "Any other reason your doctor said to avoid exercise"
                ] },
                { kind: "note", text: "If you answered YES to any item: your doctor must clear you before you start.", when: function (v) { return Object.values(v.safety || {}).includes("Yes"); } },
                { key: "cleared", label: "Doctor clearance", kind: "check", options: ["Cleared"], when: function (v) { return Object.values(v.safety || {}).includes("Yes"); } },
                { key: "cleardate", label: "Cleared on", kind: "date", when: function (v) { return Object.values(v.safety || {}).includes("Yes"); } }
            ] },
            { id: "goals", title: "My Goals (check up to 3)", fields: [
                { key: "goals", label: "Select up to three goals", kind: "check", max: 3, options: [
                    "Feel happier and less anxious", "Sleep better", "More energy", "Lose weight or prevent weight gain",
                    "Lower blood pressure, blood sugar or cholesterol", "Stronger muscles and bones", "Better focus and thinking", "Other"
                ] },
                { key: "goalother", label: "Other goal", when: function (v) { return (v.goals || []).includes("Other"); } }
            ] },
            { id: "plan", title: "My Weekly Exercise Plan", fields: [
                { key: "plan", label: "Weekly plan", kind: "grid", head: ["Type", "What it is", "How much"], inputs: [{ label: "My choice" }], rows: [
                    ["Aerobic (cardio)", "Brisk walking, cycling, swimming, dancing, jogging", "Goal: 150 minutes per week (for example 30 minutes, 5 days)"],
                    ["Strength", "Body weight, resistance bands, weights, push-ups, squats", "2 days per week, 8 to 10 exercises, 1 to 2 sets of 8 to 12 repeats"],
                    ["Stretching and balance", "Stretching, yoga, tai chi", "2 to 3 days per week, 5 to 10 minutes"],
                    ["Move more all day", "Stand up, take stairs, short walks", "Get up and move for 2 to 3 minutes every hour of sitting"]
                ] }
            ] },
            { id: "slow", title: "Start Low, Go Slow", fields: [
                { kind: "note", text: "Do not do it all at once. Build up step by step." },
                { kind: "grid", head: ["Weeks", "What to do"], rows: [
                    ["Weeks 1 to 2", "10 minutes of walking, 3 days per week"],
                    ["Weeks 3 to 4", "15 to 20 minutes, 3 to 4 days per week"],
                    ["Weeks 5 to 8", "20 to 30 minutes, 4 to 5 days per week. Add 2 strength days."],
                    ["Week 9 and after", "Aim for 150 minutes per week. Mix cardio, strength and stretching."]
                ] },
                { key: "startmin", label: "My starting point: minutes", kind: "number" },
                { key: "startact", label: "My starting point: activity" },
                { key: "startdays", label: "My starting point: days per week", kind: "number" }
            ] },
            { id: "talk", title: "How Hard Should I Work? (The Talk Test)", fields: [
                { kind: "grid", head: ["Level", "How it feels", "Talk test"], rows: [
                    ["Easy", "Light effort", "You can sing"],
                    ["Moderate (aim for this)", "Slightly breathless, warm", "You can talk but not sing"],
                    ["Hard", "Very breathless", "You can say only a few words"]
                ] },
                { kind: "note", text: "My target: Moderate. Warm up for 5 minutes first and cool down for 5 minutes after." }
            ] },
            { id: "schedule", title: "My Weekly Schedule", fields: [
                { key: "schedule", label: "Weekly schedule", kind: "grid", head: ["Day"], inputs: [
                    { label: "Activity" }, { label: "Time of day" }, { label: "Minutes", type: "number" }, { label: "Done (✔)", type: "check" }
                ], rows: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"].map(function (day) { return [day]; }) }
            ] },
            { id: "rules", title: "Safety Rules", fields: [
                { kind: "note", text: "STOP and rest if you feel: chest pain or pressure, severe shortness of breath, dizziness or fainting, a racing or irregular heartbeat, or sharp pain in a joint or muscle. If symptoms do not go away quickly, get medical help right away.\n\nDrink water before, during and after exercise.\nWear comfortable shoes and clothes.\nAvoid exercising in extreme heat, or right after a heavy meal.\nRest at least 1 day per week.\nMild muscle soreness is normal. Sharp pain is not." }
            ] },
            { id: "special", title: "Special Instructions From My Doctor", fields: [
                { key: "special", label: "Check those that apply", kind: "check", options: [
                    "Lithium: Drink plenty of fluids, avoid heavy sweating in the heat, and do not exercise to the point of dehydration.",
                    "Antipsychotics or blood pressure medicines: Stand up slowly. Dizziness after exercise can occur.",
                    "Medicines that cause drowsiness: Exercise when you feel most alert and be careful with swimming and cycling.",
                    "Diabetes medicines: Check your blood sugar and carry a snack in case of low sugar.",
                    "Weight gain from medicines: Combine exercise with the Nutrition Prescription.", "Other"
                ] },
                { key: "specialother", label: "Other instructions", when: function (v) { return (v.special || []).includes("Other"); } }
            ] },
            { id: "tips", title: "Tips for Low Energy or Low Motivation Days", fields: [
                { key: "tips", label: "Check the tips I will use", kind: "check", options: [
                    "Just put on your shoes and walk for 5 minutes. You may continue if you feel like it.",
                    "Exercise with a friend, family member or group.", "Pick something you enjoy: music, dancing, a park, a pet.",
                    "Set a reminder on your phone at the same time each day.",
                    "Plan for obstacles (rain, tiredness) and have a Plan B (indoor walking, stretching at home).",
                    "Reward yourself with something other than food."
                ] }
            ] },
            { id: "smart", title: "My SMART Goal This Week", fields: [
                { key: "sg1", label: "This week, I will (what activity)" },
                { key: "sg2", label: "For how many minutes", kind: "number" },
                { key: "sg3", label: "On how many days", kind: "number" },
                { key: "sg4", label: "At (when and where)" },
                { key: "conf", label: "Confidence I can do it (1 to 10)", kind: "radio", options: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"] },
                { kind: "note", text: "If below 7, make the goal smaller." }
            ] },
            { id: "tracker", title: "Mood and Energy Tracker", fields: [
                { kind: "note", text: "Rate from 0 (worst) to 10 (best) before and after you exercise." },
                { key: "tracker", label: "Mood and energy entries", kind: "grid", head: [], inputs: [
                    { label: "Date", type: "date" }, { label: "Activity and minutes" }, { label: "Mood before", type: "number", min: 0, max: 10 },
                    { label: "Mood after", type: "number", min: 0, max: 10 }, { label: "Energy after", type: "number", min: 0, max: 10 }, { label: "Notes" }
                ], rows: [[], [], [], [], [], []] }
            ] },
            { id: "follow", title: "Follow-Up", fields: [
                { key: "next", label: "Next visit", kind: "date" },
                { key: "contact", label: "Contact the clinic if you have", kind: "check", options: [
                    "New pain or injury", "Unusual tiredness or breathlessness", "Trouble keeping up with the plan",
                    "Worsening mood or thoughts of self-harm"
                ] },
                { kind: "note", text: "In an emergency, go to the nearest hospital or call your local emergency number." },
                { key: "docsig", label: "Doctor's signature (type name)" },
                { key: "patsig", label: "Patient's signature (type name)" }
            ] }
        ]
    };

    var nutrition = {
        type: "nutrition",
        title: "Nutrition Prescription",
        sections: [
            { id: "about", title: "About You", fields: [
                { key: "name", label: "Name", kind: "patient-name", required: true },
                { key: "age", label: "Age", kind: "age", required: true },
                { key: "sex", label: "Sex", kind: "sex", required: true },
                { key: "hwb", label: "Height / Weight / BMI" },
                { key: "allergy", label: "Food allergies or intolerances" },
                { key: "conditions", label: "Medical conditions", kind: "check", options: ["Diabetes", "High blood pressure", "High cholesterol", "Kidney disease", "Other"] },
                { key: "condother", label: "Other condition", when: function (v) { return (v.conditions || []).includes("Other"); } },
                { key: "meds", label: "Current medicines", kind: "area" }
            ] },
            { id: "goals", title: "My Main Goals (check up to 3)", fields: [
                { key: "goals", label: "Select up to three goals", kind: "check", max: 3, options: [
                    "Improve mood and energy", "Sleep better", "Lose weight / prevent weight gain from medicines",
                    "Lower blood sugar", "Lower blood pressure or cholesterol", "Eat more regularly (stop skipping meals)",
                    "Cut down on sugar, alcohol or caffeine", "Other"
                ] },
                { key: "goalother", label: "Other goal", when: function (v) { return (v.goals || []).includes("Other"); } }
            ] },
            { id: "plate", title: "My Plate (every main meal)", fields: [
                { kind: "note", text: "Use this guide on every main meal." },
                { kind: "grid", head: ["Fill this much of your plate", "With"], rows: [
                    ["½ plate", "Vegetables and fruits (any color, fresh, frozen or canned)"],
                    ["¼ plate", "Whole grains or starchy foods (brown rice, oats, whole wheat bread, sweet potato, corn)"],
                    ["¼ plate", "Protein (fish, eggs, chicken, tofu, beans, lentils, nuts)"],
                    ["Add", "A small amount of healthy fat (olive oil, avocado, nuts)"],
                    ["Drink", "Water first. Tea or coffee is fine in moderation."]
                ] }
            ] },
            { id: "targets", title: "My Daily Targets", fields: [
                { key: "targets", label: "Daily targets", kind: "grid", head: ["Food group", "Goal per day", "Examples of 1 serving"], inputs: [
                    { label: "Goal set (✔)", type: "check" }
                ], rows: [
                    ["Vegetables", "3 or more servings", "1 fist-size or 1 cup raw leafy greens"],
                    ["Fruit", "2 servings", "1 medium fruit"],
                    ["Whole grains", "3 servings", "½ cup cooked rice or oats, 1 slice bread"],
                    ["Protein", "2 to 3 palm-size portions", "1 palm of fish, chicken, tofu or 1 cup beans"],
                    ["Fish (especially oily fish)", "2 times per week", "Sardines, mackerel, salmon, tuna"],
                    ["Nuts and seeds", "1 small handful", "About 30 g"],
                    ["Water", "6 to 8 glasses", "1 glass = 250 mL"]
                ] }
            ] },
            { id: "more", title: "Eat More Of (Mood and Brain Friendly)", fields: [
                { key: "more", label: "Check those I will eat more of", kind: "check", options: [
                    "Colorful vegetables and fruits", "Beans, lentils, chickpeas", "Oily fish, eggs", "Whole grains and oats",
                    "Nuts, seeds, olive oil", "Yogurt, fermented foods (kimchi, tempeh, kefir)", "Leafy greens"
                ] }
            ] },
            { id: "less", title: "Eat Less Of", fields: [
                { key: "less", label: "Check those I will eat less of", kind: "check", options: [
                    "Sugary drinks (soda, sweetened coffee and tea, juice)", "Sweets, pastries, cookies",
                    "Fast food and deep-fried foods", "Processed meats (hotdogs, bacon, luncheon meat)",
                    "Instant noodles and packaged snacks", "Alcohol (best: none)", "Caffeine (none after 2 PM)"
                ] },
                { key: "alc", label: "Alcohol: limit to how many drinks per week", kind: "number", when: function (v) { return (v.less || []).includes("Alcohol (best: none)"); } },
                { key: "caf", label: "Caffeine: no more than how many cups per day", kind: "number", when: function (v) { return (v.less || []).includes("Caffeine (none after 2 PM)"); } }
            ] },
            { id: "swaps", title: "Easy Swaps I Will Try", fields: [
                { kind: "grid", head: ["Instead of", "I will choose"], rows: [
                    ["White rice", "Brown rice or half white, half brown"],
                    ["Soda or juice", "Water, sparkling water, unsweetened tea"],
                    ["Chips or biscuits", "Fruit, nuts, yogurt"],
                    ["Fried chicken", "Grilled or steamed chicken or fish"]
                ] },
                { key: "swapa", label: "My own swap: instead of" },
                { key: "swapb", label: "My own swap: I will choose" }
            ] },
            { id: "eating", title: "My Eating Schedule", fields: [
                { key: "eating", label: "Check those that apply", kind: "check", options: [
                    "Breakfast within 1 to 2 hours of waking", "Eat slowly and without screens when possible"
                ] },
                { key: "lunch", label: "Lunch around (time)" },
                { key: "dinner", label: "Dinner around (time, at least 2 to 3 hours before bed)" },
                { key: "snack", label: "Healthy snack (if needed)" }
            ] },
            { id: "special", title: "Special Instructions From My Doctor", fields: [
                { key: "special", label: "Check those that apply", kind: "check", options: [
                    "Weight-gaining medicines: Choose high-fiber, high-protein foods. Avoid sugary drinks and late-night eating.",
                    "Lithium: Keep salt and fluid intake steady. Drink enough water. Do not suddenly change your salt intake or go on crash diets.",
                    "MAOI medicines: Avoid aged cheese, cured meats, soy sauce, fermented foods and tap beer. Ask your doctor for the full list.",
                    "Warfarin: Keep leafy green intake consistent from week to week.",
                    "Grapefruit: Avoid, unless my doctor or pharmacist says it is safe.", "Other"
                ] },
                { key: "specialother", label: "Other instructions", when: function (v) { return (v.special || []).includes("Other"); } }
            ] },
            { id: "smart", title: "My SMART Goal This Week", fields: [
                { key: "sg1", label: "This week, I will (what)" },
                { key: "sg2", label: "How many days per week", kind: "number" },
                { key: "sg3", label: "At (when and where)" },
                { key: "conf", label: "Confidence I can do it (1 to 10)", kind: "radio", options: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"] },
                { kind: "note", text: "If below 7, make the goal smaller." }
            ] },
            { id: "checkin", title: "Weekly Check-In", fields: [
                { key: "checkin", label: "Weekly check-in", kind: "grid", head: ["Day"], inputs: [
                    { label: "Veggies (✔)", type: "check" }, { label: "Fruit (✔)", type: "check" },
                    { label: "Water (✔)", type: "check" }, { label: "Skipped a meal? (✔)", type: "check" },
                    { label: "Mood (0 to 10)", type: "number", min: 0, max: 10 }
                ], rows: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"].map(function (day) { return [day]; }) }
            ] },
            { id: "follow", title: "Follow-Up", fields: [
                { key: "next", label: "Next visit", kind: "date" },
                { key: "contact", label: "Contact the clinic if you have", kind: "check", options: [
                    "Severe loss of appetite", "Vomiting or diarrhea that does not stop", "Rapid weight change",
                    "Worsening mood or thoughts of self-harm"
                ] },
                { kind: "note", text: "In an emergency, go to the nearest hospital or call your local emergency number." },
                { key: "docsig", label: "Doctor's signature (type name)" },
                { key: "patsig", label: "Patient's signature (type name)" }
            ] }
        ]
    };

    function esc(value) {
        return String(value == null ? "" : value).replace(/[&<>"']/g, function (char) {
            return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[char];
        });
    }

    function attr(value) {
        return esc(value).replace(/`/g, "&#96;");
    }

    function valueAt(values, key) {
        return values[key] == null ? "" : values[key];
    }

    function inputHtml(field, value) {
        var type = field.kind === "date" ? "date" : field.kind === "number" || field.kind === "age" ? "number" : "text";
        var min = field.kind === "age" || field.kind === "number" ? ' min="0"' : "";
        var max = field.kind === "age" ? ' max="150"' : "";
        return '<label class="en-field"><span>' + esc(field.label) + (field.required ? ' <b class="en-required">*</b>' : "") +
            '</span><input type="' + type + '" data-key="' + attr(field.key) + '"' + min + max +
            (field.required ? " required" : "") + ' value="' + attr(value) + '"></label>';
    }

    function fieldHtml(field, values) {
        if (field.kind === "note") return '<div class="en-note">' + esc(field.text) + "</div>";
        if (field.kind === "patient-name") {
            return '<label class="en-field"><span>Name <b class="en-required">*</b></span><div class="en-typeahead">' +
                '<input type="text" data-key="name" autocomplete="off" required value="' + attr(valueAt(values, "name")) + '">' +
                '<div class="en-suggestions" role="listbox" hidden></div></div></label>';
        }
        if (field.kind === "age" || field.kind === "sex") {
            var type = field.kind === "age" ? "number" : "text";
            var minMax = field.kind === "age" ? ' min="0" max="150"' : "";
            return '<label class="en-field"><span>' + esc(field.label) + ' <b class="en-required">*</b></span><input type="' + type +
                '" data-key="' + field.kind + '"' + minMax + ' required value="' + attr(valueAt(values, field.kind)) + '"></label>';
        }
        if (field.kind === "area") {
            return '<label class="en-field en-field-wide"><span>' + esc(field.label) + '</span><textarea rows="3" data-key="' +
                attr(field.key) + '">' + esc(valueAt(values, field.key)) + "</textarea></label>";
        }
        if (field.kind === "radio" || field.kind === "check") {
            var selected = valueAt(values, field.key);
            var group = field.options.map(function (option) {
                var checked = field.kind === "radio" ? selected === option : Array.isArray(selected) && selected.includes(option);
                return '<label class="en-option"><input type="' + (field.kind === "radio" ? "radio" : "checkbox") +
                    '" data-key="' + attr(field.key) + '" data-option="' + attr(option) + '"' +
                    (field.max ? ' data-max="' + field.max + '"' : "") + (checked ? " checked" : "") +
                    '><span>' + esc(option) + "</span></label>";
            }).join("");
            return '<fieldset class="en-choice en-field-wide"><legend>' + esc(field.label) + "</legend><div class=\"en-options\">" + group + "</div></fieldset>";
        }
        if (field.kind === "yesno") {
            var answers = valueAt(values, field.key) || {};
            return '<div class="en-field-wide"><div class="en-table-wrap"><table class="en-table"><thead><tr><th>Question</th><th>Yes</th><th>No</th></tr></thead><tbody>' +
                field.rows.map(function (row, index) {
                    return "<tr><td>" + esc(row) + "</td>" + ["Yes", "No"].map(function (answer) {
                        return '<td class="en-center"><input type="radio" name="' + attr(field.key + "-" + index) + '" data-kind="yesno" data-key="' +
                            attr(field.key) + '" data-row="' + index + '" value="' + answer + '"' +
                            (answers[index] === answer ? " checked" : "") + ' aria-label="' + answer + ': ' + attr(row) + '"></td>';
                    }).join("") + "</tr>";
                }).join("") + "</tbody></table></div></div>";
        }
        if (field.kind === "grid") {
            var columns = (field.head || []).concat((field.inputs || []).map(function (input) { return input.label; }));
            var data = valueAt(values, field.key) || [];
            var body = field.rows.map(function (row, rowIndex) {
                var cells = row.map(function (text) { return "<td>" + esc(text) + "</td>"; }).join("");
                var editable = (field.inputs || []).map(function (input, columnIndex) {
                    var cellValue = data[rowIndex] && data[rowIndex][columnIndex];
                    if (input.type === "check") {
                        return '<td class="en-center"><input type="checkbox" data-kind="grid-check" data-key="' + attr(field.key) +
                            '" data-row="' + rowIndex + '" data-col="' + columnIndex + '"' + (cellValue ? " checked" : "") +
                            ' aria-label="' + attr(input.label + " " + (row[0] || "")) + '"></td>';
                    }
                    var type = input.type || (/^Date$/i.test(input.label) ? "date" : "text");
                    return '<td><input type="' + type + '" data-kind="grid-value" data-key="' + attr(field.key) +
                        '" data-row="' + rowIndex + '" data-col="' + columnIndex + '"' +
                        (input.min != null ? ' min="' + input.min + '"' : "") + (input.max != null ? ' max="' + input.max + '"' : "") +
                        ' aria-label="' + attr(input.label + " " + (row[0] || "")) + '" value="' + attr(cellValue) + '"></td>';
                }).join("");
                return "<tr>" + cells + editable + "</tr>";
            }).join("");
            return '<div class="en-field-wide"><div class="en-table-wrap"><table class="en-table"><thead><tr>' +
                columns.map(function (column) { return "<th>" + esc(column) + "</th>"; }).join("") +
                "</tr></thead><tbody>" + body + "</tbody></table></div></div>";
        }
        return inputHtml(field, valueAt(values, field.key));
    }

    function applyConditions(root, definition, values) {
        definition.sections.forEach(function (section) {
            section.fields.forEach(function (field) {
                if (!field.when) return;
                var element = root.querySelector('[data-condition="' + attr(section.id + "-" + field.key) + '"]');
                if (element) element.hidden = !field.when(values);
            });
        });
    }

    function displayField(field, values) {
        if (field.kind === "note") return '<div class="en-print-note">' + esc(field.text).replace(/\n/g, "<br>") + "</div>";
        if (field.kind === "yesno") {
            var answers = values[field.key] || {};
            return '<table class="en-print-table"><thead><tr><th>Question</th><th>Yes</th><th>No</th></tr></thead><tbody>' +
                field.rows.map(function (row, index) {
                    return "<tr><td>" + esc(row) + "</td><td>" + (answers[index] === "Yes" ? "☒" : "☐") +
                        "</td><td>" + (answers[index] === "No" ? "☒" : "☐") + "</td></tr>";
                }).join("") + "</tbody></table>";
        }
        if (field.kind === "grid") {
            var headers = (field.head || []).concat((field.inputs || []).map(function (input) { return input.label; }));
            var entries = values[field.key] || [];
            return '<table class="en-print-table"><thead><tr>' + headers.map(function (header) { return "<th>" + esc(header) + "</th>"; }).join("") +
                "</tr></thead><tbody>" + field.rows.map(function (row, rowIndex) {
                    var cells = row.map(function (item) { return "<td>" + esc(item) + "</td>"; }).join("");
                    var editable = (field.inputs || []).map(function (input, colIndex) {
                        var answer = entries[rowIndex] && entries[rowIndex][colIndex];
                        return "<td>" + (input.type === "check" ? (answer ? "☒" : "☐") : esc(answer)) + "</td>";
                    }).join("");
                    return "<tr>" + cells + editable + "</tr>";
                }).join("") + "</tbody></table>";
        }
        if (field.kind === "check") {
            var chosen = values[field.key] || [];
            return field.options.map(function (option) { return (chosen.includes(option) ? "☒ " : "☐ ") + esc(option); }).join("<br>");
        }
        if (field.kind === "radio") return esc(values[field.key] || "—");
        if (field.kind === "age") return esc(values.age || "—");
        if (field.kind === "sex") return esc(values.sex || "—");
        return esc(values[field.key] || "");
    }

    function printableDocument(definition, values, date) {
        var sections = definition.sections.map(function (section, index) {
            var content = section.fields.map(function (field) {
                if (field.when && !field.when(values)) return "";
                var label = field.label ? '<div class="en-print-label">' + esc(field.label) + "</div>" : "";
                return '<div class="en-print-field">' + label + displayField(field, values) + "</div>";
            }).join("");
            return '<section class="en-print-section"><h2>' + (index + 1) + ". " + esc(section.title) + "</h2>" + content + "</section>";
        }).join("");
        return '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>' + esc(definition.title) +
            '</title><style>@page{size:letter;margin:14mm}*{box-sizing:border-box}body{font:10pt/1.35 Arial,sans-serif;color:#111;margin:0}h1{font-size:19pt;margin:0 0 4pt}.en-print-meta{font-size:9pt;border-bottom:1px dashed #555;padding:0 0 8pt;margin-bottom:8pt}.en-print-section{break-inside:avoid-page;padding:7pt 0 5pt;border-bottom:1px solid #bbb}.en-print-section h2{font-size:12pt;margin:0 0 6pt}.en-print-field{margin:4pt 0}.en-print-label{font-weight:bold;margin-bottom:2pt}.en-print-note{white-space:normal;margin:3pt 0}.en-print-table{width:100%;border-collapse:collapse;font-size:8.5pt;margin:4pt 0;break-inside:avoid}.en-print-table td,.en-print-table th{border:1px solid #777;padding:3pt 4pt;text-align:left;vertical-align:top}.en-print-table th{background:#eee}.en-print-table td:empty:after{content:"\\\\00a0"}.en-print-footer{margin-top:10pt;font-size:8pt;font-style:italic}</style></head><body><h1>' +
            esc(definition.title) + '</h1><div class="en-print-meta">Lifestyle Medicine Clinic &nbsp; | &nbsp; Date: ' + esc(date) +
            '<br>Name: ' + esc(values.name || "—") + ' &nbsp; | &nbsp; Age: ' + esc(values.age || "—") +
            ' &nbsp; | &nbsp; Sex: ' + esc(values.sex || "—") + "</div>" + sections +
            '<div class="en-print-footer">This prescription is general guidance and does not replace individual medical advice.</div></body></html>';
    }

    function renderForm(root, definition) {
        var values = {};
        var patientRecordId = null;
        var selectedPatientName = null;
        var date = (window.PSYCH_DATA || {}).lifestylePrescriptionDate || new Date().toISOString().slice(0, 10);
        var lastSavedPayload = null;
        var lastSavedDate = date;
        var suggestions = [];
        var requestSequence = 0;
        var searchTimer = null;
        var currentMatches = [];
        var activeIndex = -1;
        var toc = definition.sections.map(function (section, index) {
            return '<a href="#' + attr(definition.type + "-" + section.id) + '">' + (index + 1) + ". " + esc(section.title) + "</a>";
        }).join("");
        var content = definition.sections.map(function (section, index) {
            var fields = section.fields.map(function (field) {
                var condition = field.when ? ' data-condition="' + attr(section.id + "-" + field.key) + '"' : "";
                var fieldClass = field.kind === "note" || field.kind === "grid" || field.kind === "yesno" || field.kind === "check" || field.kind === "radio" || field.kind === "area" ? " en-wide" : "";
                return '<div class="en-field-wrap' + fieldClass + '"' + condition + ">" + fieldHtml(field, values) + "</div>";
            }).join("");
            return '<section class="en-section" id="' + attr(definition.type + "-" + section.id) + '"><h2><span>' +
                (index + 1) + "</span>" + esc(section.title) + '</h2><div class="en-fields">' + fields + "</div></section>";
        }).join("");

        root.innerHTML = '<div class="en-prescription-layout"><main class="en-main"><header class="en-header"><div><h1>' +
            esc(definition.title) + '</h1><p>Lifestyle Medicine Clinic. Complete with your doctor. <b class="en-required">*</b> Required. <span class="en-today">Date: ' + esc(date) + '</span></p></div><div class="en-header-actions"><span class="en-save-status" aria-live="polite"></span>' +
            '<button type="button" class="en-action-button en-reset-button">Reset</button><button type="button" class="en-action-button en-save-button">Save</button><button type="button" class="en-action-button en-primary en-print-button">Print</button></div></header>' +
            '<form class="en-sheet" novalidate>' + content + "</form></main><nav class=\"en-toc\" aria-label=\"Prescription sections\">" +
            "<strong>Contents</strong>" + toc + "</nav></div>";

        var form = root.querySelector(".en-sheet");
        var status = root.querySelector(".en-save-status");
        var nameInput = form.querySelector('[data-key="name"]');
        var suggestionsBox = form.querySelector(".en-suggestions");
        root.querySelector(".en-toc").addEventListener("click", function (event) {
            var link = event.target.closest("a[href^='#']");
            if (!link) return;
            event.preventDefault();
            var section = document.getElementById(link.getAttribute("href").slice(1));
            if (section) section.scrollIntoView({ behavior: "smooth", block: "start" });
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, "", "#" + definition.type);
            }
        });

        function setStatus(message, isError) {
            status.textContent = message;
            status.classList.toggle("is-error", !!isError);
        }

        function renderSuggestions(matches) {
            currentMatches = matches;
            activeIndex = -1;
            suggestionsBox.innerHTML = "";
            if (!matches.length) {
                suggestionsBox.hidden = true;
                return;
            }
            matches.forEach(function (patient, index) {
                var option = document.createElement("button");
                option.type = "button";
                option.className = "en-suggestion";
                option.setAttribute("role", "option");
                option.dataset.index = String(index);
                option.textContent = patient.name + (patient.patient_id ? " (" + patient.patient_id + ")" : "");
                option.addEventListener("click", function () {
                    patientRecordId = patient.id;
                    values.name = patient.name;
                    values.age = patient.age == null ? "" : String(patient.age);
                    values.sex = patient.sex || "";
                    selectedPatientName = patient.name;
                    nameInput.value = values.name;
                    form.querySelector('[data-key="age"]').value = values.age;
                    form.querySelector('[data-key="sex"]').value = values.sex;
                    suggestionsBox.hidden = true;
                    setStatus("", false);
                });
                suggestionsBox.appendChild(option);
            });
            suggestionsBox.hidden = false;
        }

        function findLocalPatients(query) {
            var normalized = query.trim().toLowerCase();
            return suggestions.filter(function (patient) {
                return !normalized || (patient.name || "").toLowerCase().includes(normalized) ||
                    String(patient.patient_id || "").toLowerCase().includes(normalized);
            }).slice(0, 12);
        }

        function searchPatients(query) {
            var current = ++requestSequence;
            var routes = window.PSYCH_ROUTES || {};
            if (!query.trim()) {
                renderSuggestions(findLocalPatients(""));
                return;
            }
            var url = new URL(routes.patientsSearch || "/psychiatrist/patients/search", window.location.origin);
            url.searchParams.set("q", query);
            url.searchParams.set("limit", "12");
            fetch(url.toString(), { headers: { Accept: "application/json" } })
                .then(function (response) {
                    if (!response.ok) throw new Error("Patient search failed.");
                    return response.json();
                })
                .then(function (payload) {
                    if (current === requestSequence) {
                        renderSuggestions(payload.patients || []);
                        setStatus("", false);
                    }
                })
                .catch(function () {
                    if (current === requestSequence) {
                        renderSuggestions(findLocalPatients(query));
                        setStatus("Live patient search is unavailable; you can still enter patient details.", true);
                    }
                });
        }

        nameInput.addEventListener("focus", function () { searchPatients(nameInput.value); });
        nameInput.addEventListener("input", function () {
            values.name = nameInput.value;
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () { searchPatients(nameInput.value); }, 220);
        });
        nameInput.addEventListener("keydown", function (event) {
            if (event.key === "ArrowDown" && currentMatches.length) {
                event.preventDefault();
                activeIndex = (activeIndex + 1) % currentMatches.length;
            } else if (event.key === "ArrowUp" && currentMatches.length) {
                event.preventDefault();
                activeIndex = (activeIndex - 1 + currentMatches.length) % currentMatches.length;
            } else if (event.key === "Enter" && activeIndex >= 0 && currentMatches[activeIndex]) {
                event.preventDefault();
                suggestionsBox.querySelector('[data-index="' + activeIndex + '"]').click();
            } else if (event.key === "Escape") {
                suggestionsBox.hidden = true;
            }
            suggestionsBox.querySelectorAll(".en-suggestion").forEach(function (option, index) {
                option.classList.toggle("active", index === activeIndex);
            });
        });
        document.addEventListener("click", function (event) {
            if (!root.contains(event.target) || !event.target.closest(".en-typeahead")) suggestionsBox.hidden = true;
        });

        form.addEventListener("input", function (event) {
            var input = event.target;
            var key = input.dataset.key;
            if (!key || input.dataset.kind === "yesno" || input.dataset.kind === "grid-value" || input.dataset.kind === "grid-check") return;
            if (input.type === "radio") {
                values[key] = input.value;
            } else if (input.type === "checkbox") {
                var selection = values[key] = values[key] || [];
                var existing = selection.indexOf(input.dataset.option);
                if (input.checked && existing === -1) {
                    var limit = Number(input.dataset.max || 0);
                    if (limit && selection.length >= limit) {
                        input.checked = false;
                        setStatus("Choose no more than " + limit + " goals.", true);
                        return;
                    }
                    selection.push(input.dataset.option);
                } else if (!input.checked && existing !== -1) {
                    selection.splice(existing, 1);
                }
            } else {
                values[key] = input.value;
                if (key === "name" && patientRecordId && input.value !== selectedPatientName) patientRecordId = null;
            }
            applyConditions(root, definition, values);
            setStatus("", false);
        });

        form.addEventListener("change", function (event) {
            var input = event.target;
            var key = input.dataset.key;
            if (input.dataset.kind === "yesno") {
                values[key] = values[key] || {};
                values[key][input.dataset.row] = input.value;
            } else if (input.dataset.kind === "grid-value" || input.dataset.kind === "grid-check") {
                values[key] = values[key] || [];
                var row = Number(input.dataset.row);
                var column = Number(input.dataset.col);
                values[key][row] = values[key][row] || [];
                values[key][row][column] = input.dataset.kind === "grid-check" ? input.checked : input.value;
            }
            applyConditions(root, definition, values);
            setStatus("", false);
        });

        function validateForm() {
            var firstInvalid = null;
            definition.sections.forEach(function (section) {
                section.fields.forEach(function (field) {
                    if (!field.required || (field.when && !field.when(values))) return;
                    var empty = false;
                    if (field.kind === "yesno") {
                        var answers = values[field.key] || {};
                        empty = field.rows.some(function (row, index) { return !answers[index]; });
                    } else {
                        empty = !String(values[field.key] || "").trim();
                    }
                    if (empty && !firstInvalid) firstInvalid = form.querySelector('[data-key="' + field.key + '"]') || form;
                });
            });
            if (firstInvalid) {
                setStatus("Complete the required fields before saving or printing.", true);
                firstInvalid.scrollIntoView({ behavior: "smooth", block: "center" });
                if (firstInvalid.focus) firstInvalid.focus();
                return false;
            }
            return true;
        }

        function writePrintDocument(printWindow) {
            try {
                printWindow.document.open();
                printWindow.document.write(printableDocument(definition, values, date));
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                return true;
            } catch (error) {
                printWindow.close();
                setStatus("Saved, but the print view could not be opened.", true);
                return false;
            }
        }

        function savePrescription(shouldPrint) {
            if (!validateForm()) return;
            var payload = {
                type: definition.type,
                patient_record_id: patientRecordId,
                patient_name: values.name,
                patient_age: values.age || null,
                patient_sex: values.sex || null,
                data: values
            };
            var serializedPayload = JSON.stringify(payload);
            var printWindow = shouldPrint ? window.open("", "_blank") : null;
            if (serializedPayload === lastSavedPayload) {
                date = lastSavedDate;
                root.querySelector(".en-today").textContent = "Date: " + date;
                if (shouldPrint && printWindow) writePrintDocument(printWindow);
                else if (shouldPrint) setStatus("This version is saved. Allow pop-ups to open the print / PDF view.", true);
                else setStatus("This version is already saved.", false);
                return;
            }

            var busyControls = Array.prototype.slice.call(form.querySelectorAll("input,textarea,button"))
                .concat([root.querySelector(".en-reset-button")]);
            var priorDisabledStates = busyControls.map(function (control) {
                var wasDisabled = control.disabled;
                control.disabled = true;
                return wasDisabled;
            });
            setStatus("Saving prescription…", false);
            fetch((window.PSYCH_DATA || {}).lifestylePrescriptionStore, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            }).then(function (response) {
                return response.json().then(function (body) {
                    if (!response.ok) throw new Error(body.message || "The prescription could not be saved.");
                    return body;
                });
            }).then(function (payload) {
                date = payload.prescription_date;
                lastSavedDate = date;
                lastSavedPayload = serializedPayload;
                root.querySelector(".en-today").textContent = "Date: " + date;
                if (shouldPrint && printWindow) writePrintDocument(printWindow);
                else if (shouldPrint) setStatus("Saved. Allow pop-ups, then click Print to open the PDF view.", true);
                else setStatus("Saved to patient records.", false);
            }).catch(function (error) {
                if (printWindow) printWindow.close();
                setStatus(error.message || "The prescription could not be saved.", true);
            }).finally(function () {
                busyControls.forEach(function (control, index) {
                    control.disabled = priorDisabledStates[index];
                });
            });
        }

        root.querySelector(".en-save-button").addEventListener("click", function () { savePrescription(false); });
        root.querySelector(".en-print-button").addEventListener("click", function () { savePrescription(true); });
        root.querySelector(".en-reset-button").addEventListener("click", function () {
            if (!window.confirm("Clear all answers in this form? Saved prescriptions will not be deleted.")) return;
            Object.keys(values).forEach(function (key) { delete values[key]; });
            patientRecordId = null;
            selectedPatientName = null;
            form.reset();
            suggestionsBox.hidden = true;
            applyConditions(root, definition, values);
            setStatus("Form cleared. Previously saved prescriptions are unchanged.", false);
        });

        suggestions = Array.isArray((window.PSYCH_DATA || {}).patientSuggestions)
            ? window.PSYCH_DATA.patientSuggestions.slice() : [];
        applyConditions(root, definition, values);
    }

    renderForm(document.getElementById("exercise-prescription-app"), exercise);
    renderForm(document.getElementById("nutrition-prescription-app"), nutrition);
})();
