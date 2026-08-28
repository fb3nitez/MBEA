/* ==========================================================================
   MB.EA Integrated Psychiatric & Lifestyle Medicine Clinic
   Landing Page Logic — Bento Hero + Service Modals
   ========================================================================== */

document.addEventListener("DOMContentLoaded", function () {
    attachServiceModals();
    attachLiveClock();
});

/* --------------------------------------------------------------------------
   Live clock tile
   -------------------------------------------------------------------------- */

function attachLiveClock() {
    var timeEl = document.getElementById("clock-time");
    var dateEl = document.getElementById("clock-date");
    if (!timeEl || !dateEl) return;

    var timeFormatter = new Intl.DateTimeFormat(undefined, {
        hour: "numeric",
        minute: "2-digit",
        hour12: true,
    });

    var dateFormatter = new Intl.DateTimeFormat(undefined, {
        weekday: "long",
        month: "long",
        day: "numeric",
        year: "numeric",
    });

    function render() {
        var now = new Date();
        timeEl.textContent = timeFormatter.format(now);
        dateEl.textContent = dateFormatter.format(now);
    }

    render();
    setInterval(render, 1000 * 15);
}

/* --------------------------------------------------------------------------
   Service detail modal
   -------------------------------------------------------------------------- */

var SERVICE_DETAILS = {
    psychiatric: {
        icon: "icon-blue",
        svg: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2a3.5 3.5 0 0 0-3.5 3.5V6a3 3 0 0 0-2 5.24V13a3 3 0 0 0 2 2.83V17a3.5 3.5 0 0 0 3.5 3.5"></path><path d="M14.5 2A3.5 3.5 0 0 1 18 5.5V6a3 3 0 0 1 2 5.24V13a3 3 0 0 1-2 2.83V17a3.5 3.5 0 0 1-3.5 3.5"></path></svg>',
        title: "Psychiatric Care",
        body: "Comprehensive evaluation and treatment for depression, anxiety, bipolar disorder, schizophrenia, ADHD, and other mental health conditions — delivered by licensed psychiatrists who take the time to understand your full history before recommending a plan.",
    },
    lifestyle: {
        icon: "icon-rose",
        svg: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.6z"></path></svg>',
        title: "Lifestyle Medicine",
        body: "Evidence-based lifestyle interventions addressing sleep, nutrition, exercise, and stress. We treat the whole person, not just the symptoms — pairing medical care with sustainable, day-to-day changes.",
    },
    coaching: {
        icon: "icon-green",
        svg: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 4 13V9a2 2 0 0 1 2-2h1a4 4 0 0 1 4 4v9z"></path><path d="M11 12.5C11 8 15 4 20 4c0 5-4 9-9 8.5z"></path></svg>',
        title: "Life Coaching",
        body: "Personalized coaching programs in cognitive-behavioral coaching, stress management, and healthy habit formation — built around accountability and steady, realistic progress.",
    },
    spiritual: {
        icon: "icon-violet",
        svg: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"></path><path d="M12 18v4"></path><path d="M4.9 4.9l2.8 2.8"></path><path d="M16.3 16.3l2.8 2.8"></path><path d="M2 12h4"></path><path d="M18 12h4"></path><path d="M4.9 19.1l2.8-2.8"></path><path d="M16.3 7.7l2.8-2.8"></path></svg>',
        title: "Spiritual Wellness",
        body: "Integrating faith and spirituality into the healing process through pastoral accompaniment and biopsychosociospiritual care, for patients who want that dimension included in their treatment.",
    },
};

function attachServiceModals() {
    var overlay = document.getElementById("service-modal");
    var closeBtn = document.getElementById("modal-close");
    var iconEl = document.getElementById("modal-icon");
    var titleEl = document.getElementById("modal-title");
    var bodyEl = document.getElementById("modal-body");
    var triggers = document.querySelectorAll("[data-service]");
    var lastTrigger = null;

    if (!overlay || !triggers.length) return;

    function openModal(key, trigger) {
        var data = SERVICE_DETAILS[key];
        if (!data) return;

        lastTrigger = trigger || null;

        iconEl.className = "modal-icon " + data.icon;
        iconEl.innerHTML = data.svg;
        titleEl.textContent = data.title;
        bodyEl.textContent = data.body;

        overlay.classList.add("is-open");
        overlay.setAttribute("aria-hidden", "false");
        closeBtn.focus();

        document.addEventListener("keydown", onKeydown);
    }

    function closeModal() {
        overlay.classList.remove("is-open");
        overlay.setAttribute("aria-hidden", "true");
        document.removeEventListener("keydown", onKeydown);
        if (lastTrigger) lastTrigger.focus();
    }

    function onKeydown(e) {
        if (e.key === "Escape") closeModal();
    }

    triggers.forEach(function (trigger) {
        trigger.addEventListener("click", function () {
            openModal(trigger.getAttribute("data-service"), trigger);
        });
    });

    closeBtn.addEventListener("click", closeModal);

    overlay.addEventListener("click", function (e) {
        if (e.target === overlay) closeModal();
    });
}
