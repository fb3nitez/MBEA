/* ============================================================
   lifecoach_data.js — Shared helpers + API utilities
   Pages seed window.LC_DATA / window.LC_ROUTES from Blade.
   ============================================================ */

var LC_DATA = window.LC_DATA || {
  PATIENTS: [],
  TASKS: [],
  SCHEDULES: [],
  NOTES: [],
  COACH: {},
};

function lcShow(el) { if (el) el.classList.remove('hidden'); }
function lcHide(el) { if (el) el.classList.add('hidden'); }

function lcToast(msg) {
  var t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  lcShow(t);
  clearTimeout(t._t);
  t._t = setTimeout(function () { lcHide(t); }, 2800);
}

function lcInitials(name) {
  return String(name || '')
    .split(' ')
    .map(function (w) { return w[0]; })
    .join('')
    .slice(0, 2)
    .toUpperCase();
}

function lcPriorityBadge(p) {
  var cls = p === 'High' ? 'badge-high' : p === 'Medium' ? 'badge-medium' : 'badge-low';
  return '<span class="badge ' + cls + '">' + p + '</span>';
}

function lcStatusBadge(s) {
  var cls = s === 'Active' ? 'badge-active' : s === 'Critical' ? 'badge-critical' : 'badge-inactive';
  return '<span class="badge ' + cls + '">' + s + '</span>';
}

function lcOpenModal(id) {
  var el = document.getElementById(id);
  if (el) { lcShow(el); if (window.feather) window.feather.replace(); }
}

function lcCloseModal(id) {
  var el = document.getElementById(id);
  if (el) lcHide(el);
}

function lcRi() { if (window.feather) window.feather.replace(); }

function lcEscape(str) {
  return String(str == null ? '' : str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function lcCsrfToken() {
  var meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.getAttribute('content') : '';
}

function lcApi(url, options) {
  options = options || {};
  options.headers = Object.assign({
    'Content-Type': 'application/json',
    Accept: 'application/json',
    'X-CSRF-TOKEN': lcCsrfToken(),
    'X-Requested-With': 'XMLHttpRequest',
  }, options.headers || {});

  return fetch(url, options).then(function (res) {
    return res.json().catch(function () { return {}; }).then(function (data) {
      if (!res.ok) {
        var msg = (data && (data.message || (data.errors && Object.values(data.errors)[0]))) || 'Request failed.';
        if (Array.isArray(msg)) msg = msg[0];
        throw new Error(msg);
      }
      return data;
    });
  });
}

function lcRoute(key, id) {
  var routes = window.LC_ROUTES || {};
  var url = routes[key] || '';
  if (id != null) url = url.replace('__ID__', id).replace(':id', id);
  return url;
}

function lcFillPatientSelect(selectEl, selectedId) {
  if (!selectEl) return;
  var options = (LC_DATA.PATIENT_OPTIONS || []).length
    ? LC_DATA.PATIENT_OPTIONS
    : (LC_DATA.PATIENTS || []).map(function (p) { return { id: p.id, name: p.name }; });

  selectEl.innerHTML = '';
  if (!options.length) {
    selectEl.innerHTML = '<option value="">No assigned patients</option>';
    selectEl.disabled = true;
    return;
  }
  selectEl.disabled = false;
  options.forEach(function (p) {
    var opt = document.createElement('option');
    opt.value = p.id;
    opt.textContent = p.name;
    if (selectedId && String(selectedId) === String(p.id)) opt.selected = true;
    selectEl.appendChild(opt);
  });
}

function lcSetTopbarDate() {
  var el = document.getElementById('topbar-date') || document.querySelector('.topbar-date');
  if (!el) return;
  el.textContent = new Date().toLocaleDateString('en-US', {
    weekday: 'long', month: 'long', day: 'numeric', year: 'numeric',
  });
}

function lcInitSidebar(activeKey) {
  var map = {
    dashboard: (window.LC_ROUTES && window.LC_ROUTES.dashboard) || '/lifecoach/dashboard',
    patients: (window.LC_ROUTES && window.LC_ROUTES.patients) || '/lifecoach/patients',
    notes: (window.LC_ROUTES && window.LC_ROUTES.notes) || '/lifecoach/notes',
    tasks: (window.LC_ROUTES && window.LC_ROUTES.tasks) || '/lifecoach/tasks',
    profile: (window.LC_ROUTES && window.LC_ROUTES.profile) || '/lifecoach/profile',
  };

  document.querySelectorAll('.nav-item[data-page]').forEach(function (btn) {
    btn.classList.toggle('active', btn.getAttribute('data-page') === activeKey);
    btn.addEventListener('click', function () {
      var page = this.getAttribute('data-page');
      if (page && map[page]) window.location.href = map[page];
    });
  });

  var logout = document.getElementById('logout-btn');
  if (logout) {
    logout.addEventListener('click', function () {
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = (window.LC_ROUTES && window.LC_ROUTES.logout) || '/auth/logout';
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = '_token';
      input.value = lcCsrfToken();
      form.appendChild(input);
      document.body.appendChild(form);
      form.submit();
    });
  }

  var ham = document.getElementById('hamburger-btn');
  var sidebar = document.getElementById('sidebar');
  if (ham && sidebar) {
    ham.addEventListener('click', function () { sidebar.classList.toggle('open'); });
  }

  document.addEventListener('click', function (e) {
    var cl = e.target.closest('[data-close]');
    if (cl) { lcCloseModal(cl.getAttribute('data-close')); return; }
    if (e.target.classList.contains('modal-overlay')) {
      document.querySelectorAll('.modal-overlay').forEach(function (m) { lcHide(m); });
    }
  });

  lcSetTopbarDate();
}
