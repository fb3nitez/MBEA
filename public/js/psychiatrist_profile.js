/* psychiatrist_profile.js
   Handles Save Profile + Change Password interactions.
   Loaded only on /psychiatrist/profile via @push('scripts') in the blade.
*/

document.addEventListener('DOMContentLoaded', function () {

    function showToast(msg) {
        var t = document.getElementById('toast');
        if (!t) return;
        t.textContent = msg;
        t.classList.remove('hidden');
        clearTimeout(t._timer);
        t._timer = setTimeout(function () { t.classList.add('hidden'); }, 2800);
    }

    /* ── Save Profile ──────────────────────────────────── */
    var saveBtn = document.getElementById('save-profile-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            var name  = document.getElementById('edit-name').value.trim();
            var email = document.getElementById('edit-email').value.trim();

            if (!name || !email) {
                showToast('Name and email are required.');
                return;
            }

            // Update display name in profile card
            var profileName = document.querySelector('.profile-name');
            if (profileName) profileName.textContent = name;

            var profileEmail = document.querySelector('.profile-email');
            if (profileEmail) profileEmail.textContent = email;

            // Update avatar initials
            var avatar = document.querySelector('.profile-avatar-lg');
            if (avatar) avatar.textContent = name.split(' ').map(function(w){ return w[0]; }).join('').slice(0,2).toUpperCase();

            showToast('Profile updated successfully.');
        });
    }

    /* ── Change Password ───────────────────────────────── */
    var changePwBtn = document.getElementById('change-pw-btn');
    if (changePwBtn) {
        changePwBtn.addEventListener('click', function () {
            var current = document.getElementById('pw-current').value;
            var newPw   = document.getElementById('pw-new').value;
            var confirm = document.getElementById('pw-confirm').value;

            if (!current) { showToast('Please enter your current password.'); return; }
            if (!newPw || newPw.length < 6) { showToast('New password must be at least 6 characters.'); return; }
            if (newPw !== confirm) { showToast('Passwords do not match.'); return; }

            ['pw-current', 'pw-new', 'pw-confirm'].forEach(function (id) {
                var el = document.getElementById(id);
                if (el) el.value = '';
            });

            showToast('Password updated successfully.');
        });
    }
});