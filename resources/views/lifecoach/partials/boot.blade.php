@php
  $lcRoutes = [
    'dashboard' => route('lifecoach.dashboard'),
    'patients' => route('lifecoach.patients'),
    'notes' => route('lifecoach.notes'),
    'tasks' => route('lifecoach.tasks'),
    'profile' => route('lifecoach.profile'),
    'logout' => route('auth.logout'),
    'patientsShow' => url('/lifecoach/patients/__ID__'),
    'notesStore' => route('lifecoach.notes.store'),
    'notesDestroy' => url('/lifecoach/notes/__ID__'),
    'tasksStore' => route('lifecoach.tasks.store'),
    'tasksToggle' => url('/lifecoach/tasks/__ID__/toggle'),
    'schedulesStore' => route('lifecoach.schedules.store'),
    'goalsStore' => route('lifecoach.goals.store'),
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
    STATS: @json($stats ?? new \stdClass()),
    PATIENT_OPTIONS: @json($patientOptions ?? []),
    COACH: @json($coach ?? new \stdClass()),
  });
</script>
