/* ============================================================
   MEDCARE STAFF LOGIN — staff_login.js
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  /* ----------------------------------------------------------
     Element refs — all guarded so a missing element won't crash
  ---------------------------------------------------------- */
  var sectionPortal   = document.getElementById('section-portal');
  var sectionLogin    = document.getElementById('section-login');
  var btnGotoLogin    = document.getElementById('btn-goto-login');
  var btnBackPortal   = document.getElementById('btn-back-portal');
  var loginForm       = document.getElementById('login-form');
  var emailInput      = document.getElementById('email');
  var passwordInput   = document.getElementById('password');
  var eyeToggle       = document.getElementById('eye-toggle');
  var eyeIcon         = document.getElementById('eye-icon');
  var rememberCheck   = document.getElementById('remember-me');
  var errorBox        = document.getElementById('error-box');
  var errorMsg        = document.getElementById('error-message');
  var btnSubmit       = document.getElementById('btn-submit');
  var submitText      = document.getElementById('submit-text');
  var loadingScreen   = document.getElementById('loading-screen');
  var loadingCircle   = document.getElementById('loading-circle');
  var loadingRingSvg  = document.getElementById('loading-ring-svg');
  var loadingRoleIcon = document.getElementById('loading-role-icon');
  var loadingTextWrap = document.getElementById('loading-text');
  var loadingRoleName = document.getElementById('loading-role-name');
  var loadingStaffNm  = document.getElementById('loading-staff-name');
  var loadingProgress = document.getElementById('loading-progress-bar');
  var loadingCheck    = document.getElementById('loading-check');

  /* ----------------------------------------------------------
     Helpers
  ---------------------------------------------------------- */
  function show(el) { if (el) el.style.display = ''; el && el.classList && el.classList.remove('hidden'); }
  function hide(el) { if (el) el.classList.add('hidden'); }

  function reIcons() {
    if (window.feather) window.feather.replace();
  }

  /* ----------------------------------------------------------
     Section switching
  ---------------------------------------------------------- */
  function showPortal() {
    show(sectionPortal);
    hide(sectionLogin);
    hide(errorBox);
    reIcons();
  }

  function showLogin() {
    hide(sectionPortal);
    show(sectionLogin);
    hide(errorBox);
    reIcons();
  }

  /* ----------------------------------------------------------
     Routing — decide which section to show based on URL
  ---------------------------------------------------------- */
  function initRoute() {
    var path = window.location.pathname || '';
    if (path.indexOf('/login') !== -1) {
      showLogin();
    } else {
      showPortal();
    }
  }

  /* ----------------------------------------------------------
     Navigation button listeners
  ---------------------------------------------------------- */
  if (btnGotoLogin) {
    btnGotoLogin.addEventListener('click', function () {
      showLogin();
    });
  }

  if (btnBackPortal) {
    btnBackPortal.addEventListener('click', function (e) {
      e.preventDefault();
      showPortal();
    });
  }

  /* ----------------------------------------------------------
     Password eye toggle
  ---------------------------------------------------------- */
  var pwVisible = false;

  if (eyeToggle && passwordInput && eyeIcon) {
    eyeToggle.addEventListener('click', function () {
      pwVisible = !pwVisible;
      passwordInput.type = pwVisible ? 'text' : 'password';
      eyeIcon.setAttribute('data-feather', pwVisible ? 'eye-off' : 'eye');
      reIcons();
    });
  }

  /* ----------------------------------------------------------
     Error helpers
  ---------------------------------------------------------- */
  function showError(msg) {
    if (errorMsg) errorMsg.textContent = msg;
    show(errorBox);
  }

  function clearError() {
    hide(errorBox);
  }

  /* ----------------------------------------------------------
     Remember-me restore
  ---------------------------------------------------------- */
  var saved = '';
  try { saved = localStorage.getItem('medcare_email') || ''; } catch (e) {}
  if (saved && emailInput) {
    emailInput.value = saved;
    if (rememberCheck) rememberCheck.checked = true;
  }

  /* ----------------------------------------------------------
     Role detection
  ---------------------------------------------------------- */
  var ACCOUNTS = {
    // Short test credentials
    'test1':                   { role: 'Psychiatrist', name: 'Dr. Maria Santos', url: '/psychiatrist/dashboard' },
    'test2':                   { role: 'Life Coach',   name: 'Michael Chen',     url: '/lifecoach/dashboard'    },
    // Full email credentials
    'doctor@medcare.ph':       { role: 'Psychiatrist', name: 'Dr. Maria Santos', url: '/psychiatrist/dashboard' },
    'psychiatrist@medcare.ph': { role: 'Psychiatrist', name: 'Dr. Maria Santos', url: '/psychiatrist/dashboard' },
    'coach@medcare.ph':        { role: 'Life Coach',   name: 'Michael Chen',     url: '/lifecoach/dashboard'    },
    'lifecoach@medcare.ph':    { role: 'Life Coach',   name: 'Emily Roberts',    url: '/lifecoach/dashboard'    },
  };

  function detectRole(email) {
    var key = email.toLowerCase().trim();
    if (ACCOUNTS[key]) return ACCOUNTS[key];
    if (/doctor|psychiatrist|psych|dr\./.test(key)) {
      return { role: 'Psychiatrist', name: 'Dr. Staff Member', url: '/psychiatrist/dashboard' };
    }
    if (/coach|life/.test(key)) {
      return { role: 'Life Coach', name: 'Coach Staff Member', url: '/lifecoach/dashboard' };
    }
    return { role: 'Psychiatrist', name: 'Dr. Staff Member', url: '/psychiatrist/dashboard' };
  }

  initRoute();
  reIcons();

});
