@php
$lcRoutes = [
'dashboard' => route('lifecoach.dashboard'),
'patients' => route('lifecoach.patients'),
'notes' => route('lifecoach.notes'),
'tasks' => route('lifecoach.tasks'),
'profile' => route('lifecoach.profile'),
'profileAccount' => route('lifecoach.profile.account'),
'profileSecurity' => route('lifecoach.profile.security'),
'profileNotifications' => route('lifecoach.profile.notifications'),
'profileAvatar' => route('lifecoach.profile.avatar'),
'profileActivity' => route('lifecoach.profile.activity'),
'updates' => route('lifecoach.updates'),
'updatesReadAll' => route('lifecoach.updates.read-all'),
'updatesRead' => url('/lifecoach/updates/__ID__/read'),
'updatesDismiss' => url('/lifecoach/updates/__ID__'),
'logout' => route('auth.logout'),
'patientsShow' => url('/lifecoach/patients/__ID__'),
'notesStore' => route('lifecoach.notes.store'),
'notesDestroy' => url('/lifecoach/notes/__ID__'),
'tasksStore' => route('lifecoach.tasks.store'),
'tasksToggle' => url('/lifecoach/tasks/__ID__/toggle'),
'schedulesStore' => route('lifecoach.schedules.store'),
'goalsStore' => route('lifecoach.goals.store'),
'goalsUpdate' => url('/lifecoach/goals/__ID__'),
'goalsProgress' => url('/lifecoach/goals/__ID__/progress'),
'goalsDestroy' => url('/lifecoach/goals/__ID__'),
];
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script>
  window.LC_ROUTES = @json($lcRoutes);
  window.LC_DATA = Object.assign({}, window.LC_DATA || {}, {
    PATIENTS: @json($patients ?? []),
    TASKS: @json($tasks ?? []),
    SCHEDULES: @json($schedules ?? []),
    NOTES: @json($notes ?? []),
    STATS: @json($stats ?? (object) []),
    PATIENT_OPTIONS: @json($patientOptions ?? []),
    COACH: @json($coach ?? (object) []),
    ACTIVITY: @json($activity ?? []),
    UPDATES: @json($updates ?? (object) []),
  });
</script>