document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('profile');

  var coach = LC_DATA.COACH || {};
  var routes = window.LC_ROUTES || {};
  var currentTab = 'profile';
  var activityOffset = 0;
  var activityHasMore = true;
  var dirtyForms = new Set();

  function qs(selector, context) { return (context || document).querySelector(selector); }
  function qsa(selector, context) { return Array.prototype.slice.call((context || document).querySelectorAll(selector)); }
  function showToast(message) {
    var toast = qs('#toast');
    if (!toast) return;
    toast.textContent = message;
    toast.classList.remove('hidden');
    clearTimeout(toast._timer);
    toast._timer = setTimeout(function () { toast.classList.add('hidden'); }, 3000);
  }
  function csrfToken() { var meta = qs('meta[name="csrf-token"]'); return meta ? meta.content : ''; }
  function apiFetch(url, options) {
    options = options || {};
    options.headers = Object.assign({ 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'X-Requested-With': 'XMLHttpRequest' }, options.headers || {});
    return fetch(url, options).then(function (response) {
      return response.json().then(function (body) {
        if (!response.ok) {
          var error = new Error(body.message || 'Unable to save changes.');
          error.errors = body.errors || {};
          throw error;
        }
        return body;
      });
    });
  }
  function setBusy(form, busy) { qsa('button', form).forEach(function (button) { button.disabled = busy; }); form.classList.toggle('is-saving', busy); }
  function setStatus(type, message, error) { var el = qs('[data-form-status="' + type + '"]'); if (el) { el.textContent = message; el.classList.toggle('is-error', !!error); } }
  function clearErrors(form) { qsa('.lc-field-error,.lc-inline-error', form).forEach(function (el) { el.textContent = ''; }); qsa('.field-invalid', form).forEach(function (el) { el.classList.remove('field-invalid'); }); }
  function showErrors(form, errors) { Object.keys(errors || {}).forEach(function (key) { var field = qs('[name="' + key + '"]', form); var error = qs('[data-error-for="' + key + '"]', form) || (field && field.parentElement.querySelector('.lc-inline-error')); if (field) field.classList.add('field-invalid'); if (error) error.textContent = errors[key][0]; }); }
  function markFormState(form) { var type = form.getAttribute('data-profile-form'); var changed = formSnapshot(form) !== form._initialState; if (changed) dirtyForms.add(type); else dirtyForms.delete(type); }
  function formSnapshot(form) { var data = {}; new FormData(form).forEach(function (value, key) { data[key] = value; }); return JSON.stringify(data); }
  function updateBioCount() { var bio = qs('#profile-bio'); var count = qs('#bio-count'); if (bio && count) count.textContent = bio.value.length; }
  function formatPhone(value) { var digits = value.replace(/\D/g, '').slice(0, 11); if (!digits) return ''; if (digits.length <= 4) return digits; if (digits.length <= 7) return digits.slice(0, 4) + ' ' + digits.slice(4); return digits.slice(0, 4) + ' ' + digits.slice(4, 7) + ' ' + digits.slice(7); }
  function applyCoach(data) { coach = Object.assign({}, coach, data || {}); var name = qs('#prof-name'); var email = qs('#prof-email'); if (name) name.textContent = coach.name || ''; if (email) email.textContent = coach.email || ''; }
  function switchTab(tab, updateHash) {
    if (!qs('[data-profile-panel="' + tab + '"]')) tab = 'profile';
    currentTab = tab;
    qsa('[data-profile-tab]').forEach(function (link) { var active = link.getAttribute('data-profile-tab') === tab; link.classList.toggle('active', active); link.setAttribute('aria-selected', active ? 'true' : 'false'); });
    qsa('[data-profile-panel]').forEach(function (panel) { var active = panel.getAttribute('data-profile-panel') === tab; panel.hidden = !active; panel.classList.toggle('active', active); });
    if (updateHash && window.location.hash !== '#' + tab) history.pushState({ tab: tab }, '', '#' + tab);
    if (tab === 'activity' && !qs('#profile-activity').children.length) loadActivity(false);
    if (window.feather) window.feather.replace();
  }
  function confirmLeave(nextTab) { if (!dirtyForms.size || nextTab === currentTab) return true; return window.confirm('You have unsaved changes. Leave this section?'); }
  function renderActivity(items, append) {
    var list = qs('#profile-activity'); if (!list) return; if (!append) list.innerHTML = '';
    if (!items.length && !append) { list.innerHTML = '<div class="lc-empty-state"><i data-feather="activity"></i><strong>Your activity will appear here</strong><span>Write a coaching note or complete a task to get started.</span></div>'; return; }
    items.forEach(function (item) { var row = document.createElement('div'); row.className = 'lc-activity-row'; row.innerHTML = '<span class="lc-activity-icon"><i data-feather="' + lcEscape(item.icon || 'activity') + '"></i></span><span class="lc-activity-copy"><strong>' + lcEscape(item.label) + '</strong><small>' + lcEscape(item.detail || '') + '</small></span><time>' + lcEscape(item.date ? new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '') + '</time>'; list.appendChild(row); });
    var more = qs('#activity-load-more'); if (more) more.hidden = !activityHasMore; if (window.feather) window.feather.replace();
  }
  function loadActivity(append) { var more = qs('#activity-load-more'); if (more) more.disabled = true; apiFetch((routes.profileActivity || '') + '?offset=' + activityOffset).then(function (data) { activityHasMore = !!data.has_more; activityOffset += (data.items || []).length; renderActivity(data.items || [], append); if (more) { more.disabled = false; more.hidden = !activityHasMore; } }).catch(function () { if (more) more.disabled = false; showToast('Unable to load activity.'); }); }
  function submitForm(form, url, payload, type, after) { clearErrors(form); setBusy(form, true); setStatus(type, 'Saving...'); apiFetch(url, { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) }).then(function (data) { dirtyForms.delete(type); form._initialState = formSnapshot(form); setStatus(type, 'Saved'); showToast(data.message || 'Changes saved.'); if (after) after(data); }).catch(function (error) { setStatus(type, error.message, true); showErrors(form, error.errors); }).finally(function () { setBusy(form, false); }); }

  qsa('[data-profile-tab]').forEach(function (link, index) { link.addEventListener('click', function (event) { var tab = this.getAttribute('data-profile-tab'); if (!confirmLeave(tab)) { event.preventDefault(); return; } switchTab(tab, true); }); link.addEventListener('keydown', function (event) { if (event.key === 'ArrowDown' || event.key === 'ArrowRight') { event.preventDefault(); qsa('[data-profile-tab]')[(index + 1) % qsa('[data-profile-tab]').length].focus(); } if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') { event.preventDefault(); var links = qsa('[data-profile-tab]'); links[(index - 1 + links.length) % links.length].focus(); } if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.click(); } }); });
  window.addEventListener('hashchange', function () { var tab = window.location.hash.replace('#', '') || 'profile'; if (confirmLeave(tab)) switchTab(tab, false); });
  window.addEventListener('popstate', function () { var tab = window.location.hash.replace('#', '') || 'profile'; if (confirmLeave(tab)) switchTab(tab, false); });

  var profileForm = qs('#profile-form');
  if (profileForm) profileForm.addEventListener('submit', function (event) { event.preventDefault(); submitForm(profileForm, routes.profileAccount, { name: qs('#profile-name').value.trim(), phone: qs('#profile-phone').value.trim(), bio: qs('#profile-bio').value.trim(), email: coach.email }, 'profile', function (data) { applyCoach(data.coach); }); });
  var accountForm = qs('#account-form');
  if (accountForm) accountForm.addEventListener('submit', function (event) { event.preventDefault(); submitForm(accountForm, routes.profileAccount, { name: qs('#profile-name').value.trim(), email: qs('#account-email').value.trim(), phone: qs('#profile-phone').value.trim(), bio: qs('#profile-bio').value.trim() }, 'account', function (data) { applyCoach(data.coach); }); });
  var securityForm = qs('#security-form');
  if (securityForm) securityForm.addEventListener('submit', function (event) { event.preventDefault(); var password = qs('#new-password').value; if (password.length < 8 || password !== qs('#confirm-password').value) { setStatus('security', 'Check your password fields.', true); return; } submitForm(securityForm, routes.profileSecurity, { current_password: qs('#current-password').value, password: password, password_confirmation: qs('#confirm-password').value }, 'security', function () { securityForm.reset(); }); });
  var notificationsForm = qs('#notifications-form');
  if (notificationsForm) notificationsForm.addEventListener('submit', function (event) { event.preventDefault(); var payload = {}; new FormData(notificationsForm).forEach(function (value, key) { payload[key] = value === 'on'; }); submitForm(notificationsForm, routes.profileNotifications, payload, 'notifications'); });
  qsa('[data-profile-form]').forEach(function (form) { form._initialState = formSnapshot(form); form.addEventListener('input', function () { markFormState(form); }); });
  var phone = qs('#profile-phone'); if (phone) phone.addEventListener('input', function () { this.value = formatPhone(this.value); });
  var bio = qs('#profile-bio'); if (bio) { bio.addEventListener('input', updateBioCount); updateBioCount(); }
  qsa('[data-password-toggle]').forEach(function (button) { button.addEventListener('click', function () { var input = qs('#' + this.getAttribute('data-password-toggle')); input.type = input.type === 'password' ? 'text' : 'password'; this.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password'); }); });
  var newPassword = qs('#new-password'); if (newPassword) newPassword.addEventListener('input', function () { var hint = qs('#password-strength'); hint.textContent = this.value.length >= 12 ? 'Strong password.' : this.value.length >= 8 ? 'Good password. Add numbers or symbols for extra strength.' : 'Use at least 8 characters with a mix of letters and numbers.'; });
  var photoInput = qs('#photo-input'); var photoPreview = qs('#profile-photo-preview'); var headerAvatar = qs('#prof-avatar'); if (photoInput) photoInput.addEventListener('change', function () { var file = this.files[0]; var error = qs('#photo-error'); if (!file) return; if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2097152) { error.textContent = 'Choose a JPG, PNG, or WebP image under 2MB.'; this.value = ''; return; } error.textContent = ''; var reader = new FileReader(); reader.onload = function (event) { var background = 'url("' + event.target.result + '")'; [photoPreview, headerAvatar].forEach(function (avatar) { if (!avatar) return; avatar.style.backgroundImage = background; avatar.classList.add('has-photo'); avatar.textContent = ''; }); }; reader.readAsDataURL(file); var form = new FormData(); form.append('avatar', file); apiFetch(routes.profileAvatar, { method: 'POST', body: form }).then(function (data) { showToast(data.message); }).catch(function (error) { qs('#photo-error').textContent = error.message; }); });
  qs('#change-photo-btn')?.addEventListener('click', function () { photoInput.click(); });
  qs('#remove-photo-btn')?.addEventListener('click', function () { [photoPreview, headerAvatar].forEach(function (avatar) { if (!avatar) return; avatar.style.backgroundImage = 'none'; avatar.classList.remove('has-photo'); avatar.textContent = coach.initials || 'LC'; }); });
  qs('#activity-load-more')?.addEventListener('click', function () { loadActivity(true); });
  qsa('[data-profile-form]').forEach(function (form) { form.addEventListener('submit', function () { dirtyForms.delete(form.getAttribute('data-profile-form')); }); });
  var initialTab = window.location.hash.replace('#', '') || 'profile'; switchTab(initialTab, false); renderActivity(LC_DATA.ACTIVITY || [], false); activityOffset = (LC_DATA.ACTIVITY || []).length; activityHasMore = (LC_DATA.ACTIVITY || []).length >= 10; if (qs('#activity-load-more')) qs('#activity-load-more').hidden = !activityHasMore;
  lcRi();
});
