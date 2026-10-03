/* ==========================================================================
   MB.EA Integrated Psychiatric & Lifestyle Medicine Clinic
   Landing Page Logic — Bento Hero + Service Modals
   ========================================================================== */

document.addEventListener("DOMContentLoaded", function () {
    attachServiceModals();
});

/* --------------------------------------------------------------------------
   Service detail modal
   -------------------------------------------------------------------------- */

var SERVICE_DETAILS = {
    psychiatric: {
        icon: "icon-blue",
        svg: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2a3.5 3.5 0 0 0-3.5 3.5V6a3 3 0 0 0-2 5.24V13a3 3 0 0 0 2 2.83V17a3.5 3.5 0 0 0 3.5 3.5"></path><path d="M14.5 2A3.5 3.5 0 0 1 18 5.5V6a3 3 0 0 1 2 5.24V13a3 3 0 0 1-2 2.83V17a3.5 3.5 0 0 1-3.5 3.5"></path></svg>',
        title: "Psychiatric Care",
        body: "Compassionate, evidence-based psychiatric care for individuals experiencing emotional, behavioral, and mental health concerns. We provide personalized assessment, treatment, and ongoing support to help you regain balance, resilience, and hope.",
    },
    lifestyle: {
        icon: "icon-rose",
        svg: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.6z"></path></svg>',
        title: "Lifestyle Medicine",
        body: "We recognize that mental and physical health are deeply connected. Our lifestyle medicine approach integrates healthy nutrition, physical activity, quality sleep, stress management, meaningful relationships, and other sustainable habits that support both mental and overall well-being.",
    },
    coaching: {
        icon: "icon-green",
        svg: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 4 13V9a2 2 0 0 1 2-2h1a4 4 0 0 1 4 4v9z"></path><path d="M11 12.5C11 8 15 4 20 4c0 5-4 9-9 8.5z"></path></svg>',
        title: "Life Coaching",
        body: "Personalized guidance for individuals seeking greater clarity, purpose, growth, and direction in life. Through supportive conversations, goal-setting, and practical strategies, life coaching helps you recognize your strengths, overcome barriers, and take meaningful steps toward the life you want to build.",
    },
    spiritual: {
        icon: "icon-violet",
        svg: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"></path><path d="M12 18v4"></path><path d="M4.9 4.9l2.8 2.8"></path><path d="M16.3 16.3l2.8 2.8"></path><path d="M2 12h4"></path><path d="M18 12h4"></path><path d="M4.9 19.1l2.8-2.8"></path><path d="M16.3 7.7l2.8-2.8"></path></svg>',
        title: "Holistic Wellness Approach",
        body: "We care for the whole person—not just a diagnosis or a symptom. Our holistic approach considers the interconnectedness of the body, soul (mind, emotions), and spirit, integrating mental health care, healthy living, personal growth, relationships, values, and spirituality to support whole-person healing and wellness.",
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
