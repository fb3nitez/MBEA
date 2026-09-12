/* lifecoach_profile.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('profile');

  var coach = LC_DATA.COACH || {};

  var specsEl = document.getElementById('prof-specs');
  if (specsEl) {
    (coach.specializations || []).forEach(function (s) {
      var div = document.createElement('div');
      div.style.cssText = 'display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;color:#334155;';
      div.innerHTML = '<i data-feather="check-circle" style="width:14px;height:14px;color:#16a34a;flex-shrink:0;"></i>' + lcEscape(s);
      specsEl.appendChild(div);
    });
  }

  var actEl = document.getElementById('prof-activity');
  if (actEl) {
    var notes = (LC_DATA.NOTES || []).slice(0, 5);
    if (!notes.length) {
      actEl.innerHTML = '<div style="padding:16px;color:#94a3b8;font-size:13px;">No recent coaching notes.</div>';
    } else {
      notes.forEach(function (n) {
        var div = document.createElement('div');
        div.style.cssText = 'display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid #f1f5f9;';
        div.innerHTML =
          '<div style="width:30px;height:30px;border-radius:8px;background:#16a34a18;color:#16a34a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
            '<i data-feather="clipboard" style="width:14px;height:14px;"></i>' +
          '</div>' +
          '<div style="flex:1;">' +
            '<div style="font-size:13px;color:#334155;">' + lcEscape(n.type) + ' — ' + lcEscape(n.patient) + '</div>' +
            '<div style="font-size:11px;color:#94a3b8;margin-top:2px;">' + lcEscape(n.date) + '</div>' +
          '</div>';
        actEl.appendChild(div);
      });
    }
  }

  lcRi();
});
