@extends('layouts.psychiatrist')

@section('title', 'My Profile')
@section('page', 'profile')
@section('page_title', 'My Profile')

@section('content')
  <main class="lc-profile-page" data-psychiatrist-profile>
    <section class="profile-banner" role="img" aria-label="MB.EA Mental Health and Wellness Center">
      <img src="{{ asset('assets/mbea_banner.png') }}" alt="Welcome to MB.EA Mental Health and Wellness Center" />
    </section>
    <div class="lc-settings-layout">
      <nav class="lc-settings-nav" id="profile-nav" aria-label="Profile settings" role="tablist">
        @foreach(['profile' => ['Profile', 'user'], 'account' => ['Account', 'briefcase'], 'security' => ['Security', 'lock'], 'notifications' => ['Notifications', 'bell'], 'activity' => ['Activity', 'clock']] as $key => $item)
          <a href="#{{ $key }}" class="lc-settings-tab" data-profile-tab="{{ $key }}" role="tab"
            aria-selected="{{ $key === 'profile' ? 'true' : 'false' }}"><i
              data-feather="{{ $item[1] }}"></i><span>{{ $item[0] }}</span></a>
        @endforeach
      </nav>

      <div class="lc-settings-content">
        <section class="lc-settings-panel card active" data-profile-panel="profile" role="tabpanel" hidden>
          <div class="card-header"><span class="card-title">Profile</span><span class="lc-panel-caption">Your clinical
              profile</span></div>
          <form class="lc-settings-form" id="profile-form" data-profile-form="profile" novalidate>
            <div class="lc-photo-editor">
              <div class="profile-avatar-lg{{ $user->avatar_path ? ' has-photo' : '' }}" id="profile-photo-preview"
                style="{{ $user->avatar_path ? "background-image:url('" . e(asset('storage/' . $user->avatar_path)) . "')" : '' }}">
                {{ strtoupper(substr($user->name ?? 'DR', 0, 2)) }}</div>
              <div><strong>Profile photo</strong>
                <p>JPG, PNG, or WebP. Maximum 2MB.</p><input type="file" id="photo-input"
                  accept="image/jpeg,image/png,image/webp" class="hidden" /><button type="button" class="btn-outline-sm"
                  id="change-photo-btn">Change photo</button><button type="button" class="btn-ghost lc-remove-photo"
                  id="remove-photo-btn">Remove</button>
                <div class="lc-inline-error" id="photo-error" aria-live="polite"></div>
              </div>
            </div>
            <div class="lc-form-grid">
              <div class="field-group"><label class="field-label" for="profile-name">Full name</label><input
                  class="field-input" id="profile-name" name="name" value="{{ $user->name ?? '' }}" required /></div>
              <div class="field-group"><label class="field-label" for="profile-phone">Phone</label><input
                  class="field-input" id="profile-phone" name="phone" value="{{ $user->phone ?? '' }}"
                  placeholder="09XX XXX XXXX" /></div>
            </div>
            <div class="field-group"><label class="field-label" for="profile-bio">Short bio</label><textarea
                class="field-textarea" id="profile-bio" name="bio" maxlength="250"
                rows="4">{{ $user->bio ?? '' }}</textarea>
              <div class="lc-character-count"><span id="bio-count">0</span>/250</div>
            </div>
            <div class="lc-form-footer"><span class="lc-form-status" data-form-status="profile"
                aria-live="polite"></span><button class="btn-blue" type="submit">Save profile</button></div>
          </form>
        </section>

        <section class="lc-settings-panel card" data-profile-panel="account" role="tabpanel" hidden>
          <div class="card-header"><span class="card-title">Account</span><span class="lc-panel-caption">Member since
              {{ $user->created_at?->format('M j, Y') ?? 'your first day' }}</span></div>
          <form class="lc-settings-form" id="account-form" data-profile-form="account" novalidate>
            <div class="lc-form-grid">
              <div class="field-group"><label class="field-label" for="account-email">Email</label><input
                  class="field-input" type="email" id="account-email" name="email" value="{{ $user->email ?? '' }}"
                  required /></div>
              <div class="field-group"><label class="field-label" for="account-license">License number</label><input
                  class="field-input" id="account-license" name="license_no" value="{{ $user->license_no ?? '' }}"
                  placeholder="PRC-PSY-2026-001" /></div>
            </div>
            <div class="field-group"><label class="field-label" for="account-role">Role</label><input class="field-input"
                id="account-role" value="Psychiatrist" readonly /></div>
            <div class="lc-form-footer"><span class="lc-form-status" data-form-status="account"
                aria-live="polite"></span><button class="btn-blue" type="submit">Save changes</button></div>
          </form>
        </section>

        <section class="lc-settings-panel card" data-profile-panel="security" role="tabpanel" hidden>
          <div class="card-header"><span class="card-title">Security</span><span class="lc-panel-caption">Keep your
              account protected</span></div>
          <form class="lc-settings-form" id="security-form" data-profile-form="security" novalidate>
            @foreach([['current-password', 'current_password', 'Current password'], ['new-password', 'password', 'New password'], ['confirm-password', 'password_confirmation', 'Confirm new password']] as $password)
              <div class="field-group"><label class="field-label" for="{{ $password[0] }}">{{ $password[2] }}</label>
                <div class="lc-password-field"><input class="field-input" type="password" id="{{ $password[0] }}"
                    name="{{ $password[1] }}" autocomplete="new-password" /><button type="button" class="lc-password-toggle"
                    data-password-toggle="{{ $password[0] }}" aria-label="Show password"><i data-feather="eye"></i></button>
                </div>
                <div class="lc-field-error" data-error-for="{{ $password[1] }}" aria-live="polite"></div>
              </div>
            @endforeach
            <div class="lc-password-hint" id="password-strength">Use at least 8 characters with a mix of letters and
              numbers.</div>
            <div class="lc-form-footer"><span class="lc-form-status" data-form-status="security"
                aria-live="polite"></span><button class="btn-blue" type="submit">Update password</button></div>
          </form>
        </section>

        <section class="lc-settings-panel card" data-profile-panel="notifications" role="tabpanel" hidden>
          <div class="card-header"><span class="card-title">Notifications</span><span class="lc-panel-caption">Choose what
              reaches you</span></div>
          <form class="lc-settings-form" id="notifications-form" data-profile-form="notifications">
            @foreach([['task_reminders', 'Task reminders', 'Get reminders when follow-up tasks are due.'], ['consultation_reminders', 'Consultation reminders', 'Stay informed about upcoming consultations and follow-ups.'], ['note_followups', 'Clinical follow-ups', 'Receive reminders for recent clinical notes.']] as $notification)
              <label
                class="lc-toggle-row"><span><strong>{{ $notification[1] }}</strong><small>{{ $notification[2] }}</small></span><input
                  type="checkbox" name="{{ $notification[0] }}" {{ !empty($user->notification_preferences[$notification[0]]) ? 'checked' : '' }} /><span class="lc-switch" aria-hidden="true"></span></label>
            @endforeach
            <div class="lc-form-footer"><span class="lc-form-status" data-form-status="notifications"
                aria-live="polite"></span><button class="btn-blue" type="submit">Save preferences</button></div>
          </form>
        </section>

        <section class="lc-settings-panel card" data-profile-panel="activity" role="tabpanel" hidden>
          <div class="card-header"><span class="card-title">Activity</span><span class="lc-panel-caption">Your recent
              clinical work</span></div>
          <div class="lc-activity-list" id="profile-activity" aria-live="polite"></div><button type="button"
            class="btn-outline-sm lc-load-more" id="activity-load-more">Load more</button>
        </section>
      </div>
    </div>
  </main>

  <div class="toast hidden" id="toast" aria-live="polite"></div>
@endsection

@push('scripts')
  @php
    $profileData = [
      'name' => $user->name,
      'email' => $user->email,
      'phone' => $user->phone,
      'bio' => $user->bio,
      'license_no' => $user->license_no,
      'avatar_url' => $user->avatar_path ? asset('storage/' . $user->avatar_path) : null,
      'initials' => strtoupper(substr($user->name ?? 'DR', 0, 2)),
      'notification_preferences' => $user->notification_preferences ?: [
        'task_reminders' => true,
        'consultation_reminders' => true,
        'note_followups' => true,
      ],
    ];
  @endphp
  <script>
    window.PSYCH_DATA = window.PSYCH_DATA || {};
    window.PSYCH_DATA.profile = @json($profileData);
  </script>
  <script
    src="{{ asset('js/psych/psychiatrist_profile.js') }}?v={{ filemtime(public_path('js/psych/psychiatrist_profile.js')) }}"></script>
@endpush