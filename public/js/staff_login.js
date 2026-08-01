/* ============================================================
   MEDCARE STAFF LOGIN — polished interactions
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
  var sectionPortal = document.getElementById('section-portal');
  var sectionLogin = document.getElementById('section-login');
  var btnRolePsychiatrist = document.getElementById('btn-role-psychiatrist');
  var btnRoleCoach = document.getElementById('btn-role-coach');
  var btnBackPortal = document.getElementById('btn-back-portal');
  var loginForm = document.getElementById('login-form');
  var emailInput = document.getElementById('email');
  var passwordInput = document.getElementById('password');
  var eyeToggle = document.getElementById('eye-toggle');
  var rememberCheck = document.getElementById('remember-me');
  var errorBox = document.getElementById('error-box');
  var errorMsg = document.getElementById('error-message');
  var btnSubmit = document.getElementById('btn-submit');
  var submitText = document.getElementById('submit-text');
  var loadingScreen = document.getElementById('loading-screen');
  var loadingCircle = document.getElementById('loading-circle');
  var loadingRoleIcon = document.getElementById('loading-role-icon');
  var loadingTextWrap = document.getElementById('loading-text');
  var loadingRoleName = document.getElementById('loading-role-name');
  var loadingProgress = document.getElementById('loading-progress-bar');
  var loadingCheck = document.getElementById('loading-check');
  var selectedRoleInput = document.getElementById('selected-role');
  var roleSubtitle = document.getElementById('role-subtitle');

  var currentRole = 'Psychiatrist';

  function show(el) {
    if (!el) return;
    el.classList.remove('hidden');
    el.style.opacity = '';
    el.style.transform = '';
  }

  function hide(el) {
    if (!el) return;
    el.classList.add('hidden');
  }

  function reIcons() {
    if (window.feather) {
      window.feather.replace();
    }
  }

  function animateSection(target) {
    var current = sectionPortal.classList.contains('hidden') ? sectionLogin : sectionPortal;

    if (!target || current === target) {
      return;
    }

    current.style.transition = 'opacity 0.24s ease, transform 0.24s ease';
    current.style.opacity = '0';
    current.style.transform = 'translateY(-8px)';

    target.classList.remove('hidden');
    target.style.transition = 'opacity 0.24s ease, transform 0.24s ease';
    target.style.opacity = '0';
    target.style.transform = 'translateY(10px)';

    requestAnimationFrame(function () {
      target.style.opacity = '1';
      target.style.transform = 'translateY(0)';
    });

    window.setTimeout(function () {
      current.classList.add('hidden');
      current.style.opacity = '';
      current.style.transform = '';
      current.style.transition = '';
      target.style.transition = '';
    }, 260);
  }

  function showPortal() {
    animateSection(sectionPortal);
    hide(errorBox);
    reIcons();
  }

  function showLogin() {
    animateSection(sectionLogin);
    hide(errorBox);
    reIcons();
  }

  function initRoute() {
    var path = window.location.pathname || '';
    if (path.indexOf('/login') !== -1) {
      showLogin();
    } else {
      showPortal();
    }
  }

  function selectRole(role) {
    currentRole = role;
    if (selectedRoleInput) {
      selectedRoleInput.value = role;
    }
    if (roleSubtitle) {
      roleSubtitle.textContent = role === 'Life Coach' ? 'Coaching sessions and lifestyle tracking' : 'Patient assessments and prescriptions';
    }
    showLogin();
  }

  if (btnRolePsychiatrist) {
    btnRolePsychiatrist.addEventListener('click', function (e) {
      e.preventDefault();
      selectRole('Psychiatrist');
    });
  }

  if (btnRoleCoach) {
    btnRoleCoach.addEventListener('click', function (e) {
      e.preventDefault();
      selectRole('Life Coach');
    });
  }

  if (btnBackPortal) {
    btnBackPortal.addEventListener('click', function (e) {
      e.preventDefault();
      showPortal();
    });
  }

  var pwVisible = false;

  if (eyeToggle && passwordInput) {
    eyeToggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      pwVisible = !pwVisible;
      passwordInput.type = pwVisible ? 'text' : 'password';
      eyeToggle.textContent = pwVisible ? 'Hide' : 'Show';

      if (passwordInput.value) {
        passwordInput.focus();
        var len = passwordInput.value.length;
        passwordInput.setSelectionRange(len, len);
      }
    });
  }

  function showError(msg) {
    if (errorMsg) {
      errorMsg.textContent = msg;
    }
    show(errorBox);
  }

  function clearError() {
    hide(errorBox);
  }

  var saved = '';
  try {
    saved = localStorage.getItem('medcare_email') || '';
  } catch (e) {}

  if (saved && emailInput) {
    emailInput.value = saved;
    if (rememberCheck) {
      rememberCheck.checked = true;
    }
  }

  var ACCOUNTS = {
    test1: { role: 'Psychiatrist', name: 'Dr. Maria Santos', url: '/psychiatrist/dashboard' },
    test2: { role: 'Life Coach', name: 'Michael Chen', url: '/lifecoach/dashboard' },
    'doctor@medcare.ph': { role: 'Psychiatrist', name: 'Dr. Maria Santos', url: '/psychiatrist/dashboard' },
    'psychiatrist@medcare.ph': { role: 'Psychiatrist', name: 'Dr. Maria Santos', url: '/psychiatrist/dashboard' },
    'coach@medcare.ph': { role: 'Life Coach', name: 'Michael Chen', url: '/lifecoach/dashboard' },
    'lifecoach@medcare.ph': { role: 'Life Coach', name: 'Emily Roberts', url: '/lifecoach/dashboard' }
  };

  function detectRole(email) {
    var key = (email || '').toLowerCase().trim();

    if (ACCOUNTS[key]) {
      return ACCOUNTS[key];
    }

    if (/doctor|psychiatrist|psych|dr\./.test(key)) {
      return { role: 'Psychiatrist', name: 'Dr. Staff Member', url: '/psychiatrist/dashboard' };
    }

    if (/coach|life/.test(key)) {
      return { role: 'Life Coach', name: 'Coach Staff Member', url: '/lifecoach/dashboard' };
    }

    return currentRole === 'Life Coach' ? { role: 'Life Coach', name: 'Coach Staff Member', url: '/lifecoach/dashboard' } : { role: 'Psychiatrist', name: 'Dr. Staff Member', url: '/psychiatrist/dashboard' };
  }

  function showLoading(roleInfo) {
    if (!loadingScreen) return;

    loadingScreen.classList.remove('hidden');
    loadingTextWrap.classList.add('hidden');
    loadingCheck.classList.add('hidden');
    loadingProgress.style.width = '0%';

    loadingCircle.className = 'loading-circle';
    loadingCircle.classList.add(roleInfo.role === 'Life Coach' ? 'green-role' : 'blue-role');

    if (loadingRoleIcon) {
      loadingRoleIcon.setAttribute('data-feather', roleInfo.role === 'Life Coach' ? 'heart' : 'cpu');
    }

    if (loadingRoleName) {
      loadingRoleName.textContent = roleInfo.role || 'Psychiatrist';
      loadingRoleName.className = 'loading-role';
      loadingRoleName.classList.add(roleInfo.role === 'Life Coach' ? 'green-role' : 'blue-role');
    }

    reIcons();

    window.setTimeout(function () {
      loadingTextWrap.classList.remove('hidden');
    }, 180);

    var progress = 0;
    var timer = window.setInterval(function () {
      progress += Math.random() * 16 + 8;
      if (progress > 100) {
        progress = 100;
      }
      loadingProgress.style.width = progress + '%';

      if (progress >= 100) {
        window.clearInterval(timer);
        loadingCheck.classList.remove('hidden');
      }
    }, 120);

    window.setTimeout(function () {
      window.location.href = roleInfo.url;
    }, 1500);
  }

  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
      clearError();

      var email = emailInput && emailInput.value ? emailInput.value.trim() : '';
      var password = passwordInput ? passwordInput.value : '';

      if (!email || !password) {
        showError('Please enter your email and password.');
        return;
      }

      if (rememberCheck && rememberCheck.checked && emailInput) {
        try {
          localStorage.setItem('medcare_email', email);
        } catch (err) {}
      }

      if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.classList.add('is-loading');
      }

      if (submitText) {
        submitText.textContent = 'Signing in...';
      }

      var roleInfo = detectRole(email);
      showLoading(roleInfo);
    });
  }

  initRoute();
  reIcons();
});