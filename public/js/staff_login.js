/* ============================================================
   MB.EA STAFF LOGIN — clean two-phase, no session until confirmed
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {

    var sectionPortal       = document.getElementById("section-portal");
    var sectionLogin        = document.getElementById("section-login");
    var btnRolePsychiatrist = document.getElementById("btn-role-psychiatrist");
    var btnRoleCoach        = document.getElementById("btn-role-coach");
    var btnBackPortal       = document.getElementById("btn-back-portal");
    var loginForm           = document.getElementById("login-form");
    var emailInput          = document.getElementById("email");
    var passwordInput       = document.getElementById("password");
    var eyeToggle           = document.getElementById("eye-toggle");
    var rememberCheck       = document.getElementById("remember-me");
    var errorBox            = document.getElementById("error-box");
    var errorMsg            = document.getElementById("error-message");
    var btnSubmit           = document.getElementById("btn-submit");
    var submitText          = document.getElementById("submit-text");
    var selectedRoleInput   = document.getElementById("selected-role");
    var roleSubtitle        = document.getElementById("role-subtitle");
    var rolePill            = document.getElementById("role-pill");
    var loginRoleIcon       = document.getElementById("login-role-icon");

    // Loading screen
    var loadingScreen       = document.getElementById("loading-screen");
    var loadingCircle       = document.getElementById("loading-circle");
    var loadingRoleIconSpin = document.getElementById("loading-role-icon-spinner");
    var loadingTextWrap     = document.getElementById("loading-text");
    var loadingRoleName     = document.getElementById("loading-role-name");
    var loadingProgress     = document.getElementById("loading-progress-bar");
    var loadingCheck        = document.getElementById("loading-check");

    // Mismatch modal
    var mismatchOverlay     = document.getElementById("mismatch-overlay");
    var mismatchSelected    = document.getElementById("mismatch-selected");
    var mismatchActual      = document.getElementById("mismatch-actual");
    var mismatchCorrectLbl  = document.getElementById("mismatch-correct-label");
    var mismatchYes         = document.getElementById("mismatch-yes");
    var mismatchNo          = document.getElementById("mismatch-no");

    var currentRole = "Psychiatrist";

    // ── Helpers ──────────────────────────────────────────────
    function show(el) { if (el) el.classList.remove("hidden"); }
    function hide(el) { if (el) el.classList.add("hidden"); }
    function reIcons() { if (window.feather) window.feather.replace(); }

    function getCsrf() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute("content") : "";
    }

    function resetBtn() {
        if (btnSubmit)  { btnSubmit.disabled = false; btnSubmit.classList.remove("is-loading"); }
        if (submitText) submitText.textContent = "Sign In";
    }

    function showError(msg) {
        if (errorMsg) errorMsg.textContent = msg;
        show(errorBox);
    }

    function clearError() { hide(errorBox); }

    // ── Section transitions ───────────────────────────────────
    function animateToSection(target) {
        var current = sectionPortal.classList.contains("hidden") ? sectionLogin : sectionPortal;
        if (!target || current === target) return;

        current.style.transition = "opacity 0.22s ease, transform 0.22s ease";
        current.style.opacity    = "0";
        current.style.transform  = "translateY(-8px)";

        show(target);
        target.style.transition = "opacity 0.22s ease, transform 0.22s ease";
        target.style.opacity    = "0";
        target.style.transform  = "translateY(10px)";

        requestAnimationFrame(function () {
            target.style.opacity   = "1";
            target.style.transform = "translateY(0)";
        });

        setTimeout(function () {
            hide(current);
            current.style.cssText   = "";
            target.style.transition = "";
        }, 240);
    }

    function showPortal() {
        clearError();
        animateToSection(sectionPortal);
        reIcons();
    }

    function showLoginFor(role) {
        currentRole = role;
        if (selectedRoleInput) selectedRoleInput.value = role;

        if (rolePill) {
            rolePill.textContent = role;
            rolePill.className   = "role-pill " + (role === "Life Coach" ? "role-pill-green" : "role-pill-blue");
        }

        if (roleSubtitle) {
            roleSubtitle.textContent = role === "Life Coach"
                ? "Coaching sessions and lifestyle tracking"
                : "Patient assessments and prescriptions";
        }

        if (loginRoleIcon) {
            loginRoleIcon.setAttribute("data-feather", role === "Life Coach" ? "heart" : "cpu");
        }

        clearError();
        animateToSection(sectionLogin);
        reIcons();
    }

    // ── Role selection ────────────────────────────────────────
    if (btnRolePsychiatrist) {
        btnRolePsychiatrist.addEventListener("click", function (e) {
            e.preventDefault();
            showLoginFor("Psychiatrist");
        });
    }

    if (btnRoleCoach) {
        btnRoleCoach.addEventListener("click", function (e) {
            e.preventDefault();
            showLoginFor("Life Coach");
        });
    }

    if (btnBackPortal) {
        btnBackPortal.addEventListener("click", function (e) {
            e.preventDefault();
            showPortal();
        });
    }

    // ── Password toggle ───────────────────────────────────────
    var pwVisible = false;
    if (eyeToggle && passwordInput) {
        eyeToggle.addEventListener("click", function (e) {
            e.preventDefault(); e.stopPropagation();
            pwVisible = !pwVisible;
            passwordInput.type    = pwVisible ? "text" : "password";
            eyeToggle.textContent = pwVisible ? "Hide" : "Show";
            if (passwordInput.value) {
                passwordInput.focus();
                passwordInput.setSelectionRange(passwordInput.value.length, passwordInput.value.length);
            }
        });
    }

    // ── Remember me restore ───────────────────────────────────
    try {
        var saved = localStorage.getItem("mbea_email") || "";
        if (saved && emailInput) {
            emailInput.value = saved;
            if (rememberCheck) rememberCheck.checked = true;
        }
    } catch (e) {}

    // ── Loading screen ────────────────────────────────────────
    function showLoading(role) {
        if (!loadingScreen) return;
        show(loadingScreen);
        hide(loadingTextWrap);
        hide(loadingCheck);
        if (loadingProgress) loadingProgress.style.width = "0%";

        var isCoach = role === "Life Coach";
        if (loadingCircle)      loadingCircle.className = "loading-circle " + (isCoach ? "green-role" : "blue-role");
        if (loadingRoleIconSpin) loadingRoleIconSpin.setAttribute("data-feather", isCoach ? "heart" : "cpu");
        if (loadingRoleName)    loadingRoleName.textContent = role;
        if (loadingRoleName)    loadingRoleName.className   = "loading-role " + (isCoach ? "green-role" : "blue-role");

        reIcons();
        setTimeout(function () { show(loadingTextWrap); }, 180);

        var progress = 0;
        var timer = setInterval(function () {
            progress += Math.random() * 16 + 8;
            if (progress > 100) progress = 100;
            if (loadingProgress) loadingProgress.style.width = progress + "%";
            if (progress >= 100) { clearInterval(timer); show(loadingCheck); }
        }, 120);
    }

    // ── Real login (Phase 2) — only called after role is confirmed ──
    function doRealLogin(email, role, onSuccess, onFail) {
        if (submitText) submitText.textContent = "Signing in...";

        var formData = new FormData(loginForm);

        fetch("/auth/login", {
            method: "POST",
            credentials: "same-origin",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": getCsrf(),
            },
        })
        .then(function (res) {
            var url    = res.url || "";
            var failed = url.indexOf("/login") !== -1 || url.indexOf("/staff") !== -1;
            if (failed) throw new Error("Auth failed after role check.");
            return url;
        })
        .then(function (dashboardUrl) {
            if (rememberCheck && rememberCheck.checked) {
                try { localStorage.setItem("mbea_email", email); } catch (e) {}
            }
            showLoading(role);
            setTimeout(function () { window.location.href = dashboardUrl; }, 1500);
        })
        .catch(function (err) {
            console.error("[staff_login] Phase 2 error:", err);
            if (onFail) onFail();
        });
    }

    // ── Mismatch modal ────────────────────────────────────────
    function showMismatch(selectedRole, actualRole, email) {
        if (mismatchSelected)  mismatchSelected.textContent  = selectedRole;
        if (mismatchActual)    mismatchActual.textContent     = actualRole;
        if (mismatchCorrectLbl) mismatchCorrectLbl.textContent = actualRole;
        show(mismatchOverlay);
        reIcons();

        // "Yes — take me there" → NOW do the real login for the correct role
        if (mismatchYes) {
            mismatchYes.onclick = function () {
                hide(mismatchOverlay);
                if (btnSubmit) { btnSubmit.disabled = true; btnSubmit.classList.add("is-loading"); }
                doRealLogin(email, actualRole, null, function () {
                    resetBtn();
                    if (passwordInput) passwordInput.value = "";
                    showError("Sign in failed. Please try again.");
                });
            };
        }

        // "No — go back" → close modal, clear form, stay on login. NO session created.
        if (mismatchNo) {
            mismatchNo.onclick = function () {
                hide(mismatchOverlay);
                resetBtn();
                if (passwordInput) passwordInput.value = "";
                clearError();
            };
        }
    }

    // Close on backdrop click — same as No
    if (mismatchOverlay) {
        mismatchOverlay.addEventListener("click", function (e) {
            if (e.target === mismatchOverlay) {
                hide(mismatchOverlay);
                resetBtn();
                if (passwordInput) passwordInput.value = "";
                clearError();
            }
        });
    }

    // ── TWO-PHASE LOGIN ───────────────────────────────────────
    /*
     * PHASE 1 — /auth/check-role
     *   Verifies credentials + returns actual role from DB.
     *   Does NOT create a session (no Auth::attempt).
     *
     *   Result A: null role → wrong credentials → show error, stop.
     *   Result B: role matches currentRole → PHASE 2 immediately.
     *   Result C: role mismatches → show mismatch modal.
     *             NO real login yet. User chooses:
     *             - Yes → PHASE 2 runs now with the correct role
     *             - No  → nothing, form resets, no session anywhere
     *
     * PHASE 2 — /auth/login
     *   Only called after Phase 1 confirms identity AND:
     *   either (a) role matched, or (b) user accepted the mismatch.
     *   This is the ONLY place Auth::attempt fires.
     */

    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();
            clearError();

            var email    = emailInput    ? emailInput.value.trim() : "";
            var password = passwordInput ? passwordInput.value     : "";

            if (!email || !password) {
                showError("Please enter your email and password.");
                return;
            }

            if (btnSubmit)  { btnSubmit.disabled = true; btnSubmit.classList.add("is-loading"); }
            if (submitText) submitText.textContent = "Verifying...";

            /* ── PHASE 1 ── */
            fetch("/auth/check-role", {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type":     "application/json",
                    "Accept":           "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN":     getCsrf(),
                },
                body: JSON.stringify({ email: email, password: password }),
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                var actualRole = data.role; // "Psychiatrist" | "Life Coach" | null

                // Wrong credentials — stop everything
                if (!actualRole) {
                    resetBtn();
                    showError("Invalid email or password. Please try again.");
                    return;
                }

                // Role matches — go straight to real login
                if (actualRole === currentRole) {
                    doRealLogin(email, actualRole, null, function () {
                        resetBtn();
                        showError("Sign in failed. Please try again.");
                    });
                    return;
                }

                // Role mismatch — show modal, do NOT login yet
                // User must explicitly confirm before any session is created
                resetBtn();
                showMismatch(currentRole, actualRole, email);
            })
            .catch(function (err) {
                console.error("[staff_login] Phase 1 error:", err);
                resetBtn();
                showError("Could not verify credentials. Please try again.");
            });
        });
    }

    showPortal();
    reIcons();
});