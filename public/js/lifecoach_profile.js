/* lifecoach_profile.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('profile');

  var coach = LC_DATA.COACH;

  // Render specializations
  var specsEl = document.getElementById('prof-specs');
  if (specsEl) {
    coach.specializations.forEach(function(s) {
      var div = document.createElement('div');
      div.style.cssText = 'display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;color:#334155;';
      div.innerHTML = '<i data-feather="check-circle" style="width:14px;height:14px;color:#16a34a;flex-shrink:0;"></i>' + s;
      specsEl.appendChild(div);
    });
  }

  // Render recent activity from notes + tasks
  var actEl = document.getElementById('prof-activity');
  if (actEl) {
    var activities = [];

    LC_DATA.globalNotes().slice(0, 3).forEach(function(n) {
      activities.push({ icon: 'clipboard', color: '#16a34a', text: 'Added ' + n.type + ' note for ' + n.patient, date: n.date });
    });
    LC_DATA.TASKS.slice(0, 2).forEach(function(t) {
      activities.push({ icon: 'check-square', color: '#2563eb', text: 'Task assigned: ' + t.desc.substring(0, 45) + '...', date: t.due });
    });

    activities.forEach(function(a) {
      var div = document.createElement('div');
      div.style.cssText = 'display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid #f1f5f9;';
      div.innerHTML =
        '<div style="width:30px;height:30px;border-radius:8px;background:' + a.color + '18;color:' + a.color + ';display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
          '<i data-feather="' + a.icon + '" style="width:14px;height:14px;"></i>' +
        '</div>' +
        '<div style="flex:1;">' +
          '<div style="font-size:13px;color:#334155;">' + a.text + '</div>' +
          '<div style="font-size:11px;color:#94a3b8;margin-top:2px;">' + a.date + '</div>' +
        '</div>';
      actEl.appendChild(div);
    });
  }

  // Save Profile
  var saveBtn = document.getElementById('save-profile-btn');
  if (saveBtn) {
    saveBtn.addEventListener('click', function() {
      var name  = document.getElementById('edit-name').value.trim();
      var email = document.getElementById('edit-email').value.trim();
      if (!name || !email) { lcToast('Name and email are required.'); return; }

      // Update display
      var profName = document.getElementById('prof-name');
      var profEmail = document.getElementById('prof-email');
      var profAvatar = document.getElementById('prof-avatar');
      if (profName)   profName.textContent  = name;
      if (profEmail)  profEmail.textContent = email;
      if (profAvatar) profAvatar.textContent = lcInitials(name);

      lcToast('Profile updated successfully.');
    });
  }

  // Change Password
  var changePwBtn = document.getElementById('change-pw-btn');
  if (changePwBtn) {
    changePwBtn.addEventListener('click', function() {
      var current = document.getElementById('pw-current').value;
      var newPw   = document.getElementById('pw-new').value;
      var confirm = document.getElementById('pw-confirm').value;

      if (!current) { lcToast('Please enter your current password.'); return; }
      if (!newPw || newPw.length < 6) { lcToast('New password must be at least 6 characters.'); return; }
      if (newPw !== confirm) { lcToast('Passwords do not match.'); return; }

      ['pw-current','pw-new','pw-confirm'].forEach(function(id){
        var el = document.getElementById(id); if (el) el.value = '';
      });
      lcToast('Password updated successfully.');
    });
  }

  lcRi();
});
