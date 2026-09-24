<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLifestyleAssessmentRequest;
use App\Http\Requests\UpdateMedicalHistoryRequest;
use App\Http\Requests\UpdatePsychiatricHistoryRequest;
use App\Http\Requests\UpdateSpiritualIntakeRequest;
use App\Models\BiopsychosocialAssessment;
use App\Models\ClinicalTemplate;
use App\Models\ConsultationSchedule;
use App\Models\PatientRecord;
use App\Models\Prescription;
use App\Models\User;
use App\Services\CoachUpdateService;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PsychiatristController extends Controller
{
    public function __construct(private PatientService $patientService, private CoachUpdateService $coachUpdateService) {}

    public function dashboard(): View
    {
        $todayPatientsPage = $this->patientService->getPaginatedTodayPatients(10);
        $pendingConsultations = $this->patientService->getPendingConsultations();
        $highRiskPatients = $this->patientService->getHighRiskPatients();
        $consultationCounts = $this->patientService->getConsultationCounts();

        return view('psychiatrist.dashboard', [
            'patients' => $todayPatientsPage->getCollection(),
            'todayPatientsPaginator' => $todayPatientsPage->appends(request()->query()),
            'todayPatientsTotal' => $todayPatientsPage->total(),
            'pendingConsultations' => $pendingConsultations,
            'highRiskPatients' => $highRiskPatients,
            'pendingCount' => $consultationCounts['pending'],
            'completedCount' => $consultationCounts['completed'],
            'highRiskCount' => $highRiskPatients->count(),
            'lifeCoaches' => $this->patientService->getLifeCoaches(),
            'patientSuggestions' => $this->patientService->searchPatients(null, 12),
        ]);
    }

    public function patients(): View
    {
        $patientsPage = $this->patientService->getPaginatedPatients(10);
        $patients = $patientsPage->getCollection()
            ->map(fn(PatientRecord $p) => $this->patientService->patientToArray($p))
            ->values();

        return view('psychiatrist.patients', [
            'patients' => $patients,
            'patientsPaginator' => $patientsPage->appends(request()->query()),
            'lifeCoaches' => $this->patientService->getLifeCoaches(),
        ]);
    }

    public function consultations(): View
    {
        $consultationsPage = $this->patientService->getPaginatedConsultations(10);
        $consultations = $consultationsPage->getCollection()
            ->map(fn($c) => $this->patientService->consultationToArray($c))
            ->values();

        return view('psychiatrist.consultations', [
            'consultations' => $consultations,
            'consultationsPaginator' => $consultationsPage->appends(request()->query()),
            'patientSuggestions' => $this->patientService->searchPatients(null, 12),
            'lifeCoaches' => $this->patientService->getLifeCoaches(),
        ]);
    }

    public function lifestyle(): View
    {
        return view('psychiatrist.lifestyle', [
            'lifestylePatients' => $this->patientService->getLifestylePatients(),
            'patientSuggestions' => $this->patientService->searchPatients(null, 12),
        ]);
    }

    public function assessments(Request $request): View
    {
        $patientsPage = $this->patientService->getPaginatedAssessmentPatients(
            12,
            $request->string('q')->toString() ?: null
        );

        return view('psychiatrist.assessments', [
            'patients' => collect($patientsPage->items())->values(),
            'assessmentsPaginator' => $patientsPage->appends($request->query()),
            'assessSearch' => $request->string('q')->toString(),
        ]);
    }

    public function prescriptions(): View
    {
        $templates = $this->patientService->getClinicalTemplates();

        $user = auth()->user();

        return view('psychiatrist.prescriptions', [
            'patientSuggestions' => $this->patientService->searchPatients(null, 12),
            'rxTemplates' => $templates->where('type', 'rx')->values(),
            'dxTemplates' => $templates->where('type', 'dx')->values(),
            'prescriber' => $user,
            'prescriberData' => [
                'name' => $user->name,
                'license_no' => $user->license_no,
                'email' => $user->email,
                'clinic' => 'MB.EA Wellness Center',
                'clinic_sub' => 'Mental Health and Wellness Clinic',
                'clinic_email' => 'mbea.psychclinic@gmail.com',
                'contact_note' => 'for appointments and inquiries',
            ],
        ]);
    }

    public function toggleClinicalTemplateFavorite(int $id): JsonResponse
    {
        $template = ClinicalTemplate::findOrFail($id);
        $favorite = (bool) request()->boolean('favorite');
        $this->patientService->setClinicalTemplateFavorite($template, $favorite);

        return response()->json(['message' => 'Favorite updated.', 'favorite' => $favorite]);
    }

    public function markClinicalTemplateUsed(int $id): JsonResponse
    {
        $template = ClinicalTemplate::findOrFail($id);
        $this->patientService->recordClinicalTemplateUse($template);

        return response()->json(['message' => 'Template usage recorded.']);
    }

    public function duplicateClinicalTemplate(int $id): JsonResponse
    {
        $template = ClinicalTemplate::findOrFail($id);
        $copy = $this->patientService->createClinicalTemplate([
            'type' => $template->type,
            'name' => $template->name.' Copy',
            'tag' => $template->tag,
            'description' => $template->description,
            'payload' => $template->payload,
            'sort_order' => $template->sort_order,
        ]);

        return response()->json(['message' => 'Template duplicated.', 'template' => $copy->toArray()], 201);
    }

    public function renameClinicalTemplateTag(Request $request): JsonResponse
    {
        $data = $request->validate(['from' => ['required', 'string', 'max:100'], 'to' => ['required', 'string', 'max:100']]);
        $from = trim($data['from']);
        $to = trim(ucwords(strtolower($data['to'])));
        DB::table('clinical_templates')->where('tag', $from)->update(['tag' => $to, 'updated_at' => now()]);

        return response()->json(['message' => 'Tag updated.']);
    }

    public function deleteClinicalTemplateTag(Request $request): JsonResponse
    {
        $tag = trim($request->validate(['tag' => ['required', 'string', 'max:100']])['tag']);
        DB::table('clinical_templates')->where('tag', $tag)->update(['tag' => null, 'updated_at' => now()]);

        return response()->json(['message' => 'Tag removed from templates.']);
    }

    public function bulkDeleteClinicalTemplates(Request $request): JsonResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer', 'exists:clinical_templates,id']])['ids'];
        ClinicalTemplate::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Selected templates deleted.']);
    }

    public function bulkTagClinicalTemplates(Request $request): JsonResponse
    {
        $data = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer', 'exists:clinical_templates,id'], 'tag' => ['nullable', 'string', 'max:100']]);
        $tag = trim((string) $data['tag']);
        ClinicalTemplate::whereIn('id', $data['ids'])->update(['tag' => $tag === '' ? null : ucwords(strtolower($tag)), 'updated_at' => now()]);

        return response()->json(['message' => 'Selected template tags updated.']);
    }

    public function profile()
    {
        $user = auth()->user();

        $totalPatients = PatientRecord::count();
        $totalConsultations = ConsultationSchedule::count();
        $totalAssessments = BiopsychosocialAssessment::count();

        $recentConsultations = ConsultationSchedule::with('patientRecord')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        return view('psychiatrist.profile', compact(
            'user',
            'totalPatients',
            'totalConsultations',
            'totalAssessments',
            'recentConsultations'
        ));
    }

    public function updateProfileAccount(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.auth()->id()],
            'phone' => ['nullable', 'regex:/^09\d{2} \d{3} \d{4}$/'],
            'bio' => ['nullable', 'string', 'max:250'],
            'license_no' => ['nullable', 'string', 'max:100'],
        ]);

        $user = auth()->user();
        $user->update($data);

        return response()->json(['message' => 'Account details saved.', 'user' => $this->profileUserData($user)]);
    }

    public function updateProfileSecurity(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update(['password' => $data['password']]);

        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function updateProfileNotifications(Request $request): JsonResponse
    {
        $data = $request->validate([
            'task_reminders' => ['required', 'boolean'],
            'consultation_reminders' => ['required', 'boolean'],
            'note_followups' => ['required', 'boolean'],
        ]);

        auth()->user()->update(['notification_preferences' => $data]);

        return response()->json(['message' => 'Notification preferences saved.', 'preferences' => $data]);
    }

    public function uploadProfileAvatar(Request $request): JsonResponse
    {
        $image = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ])['avatar'];
        $user = auth()->user();
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }
        $path = $image->store('profile-avatars', 'public');
        $user->update(['avatar_path' => $path]);

        return response()->json(['message' => 'Profile photo updated.', 'avatar_url' => asset('storage/'.$path)]);
    }

    public function profileActivity(Request $request): JsonResponse
    {
        $offset = max(0, $request->integer('offset', 0));
        $activity = collect()
            ->merge(ConsultationSchedule::with('patientRecord')
                ->latest('date')
                ->latest('time')
                ->get()
                ->map(fn (ConsultationSchedule $consultation) => [
                    'icon' => 'calendar',
                    'label' => 'Consultation scheduled',
                    'detail' => $consultation->patientRecord?->fullname ?? 'Patient',
                    'date' => optional($consultation->date)->toIso8601String(),
                ]))
            ->merge(Prescription::with('patientRecord')
                ->latest('created_at')
                ->get()
                ->map(fn (Prescription $prescription) => [
                    'icon' => 'file-text',
                    'label' => 'Prescription saved',
                    'detail' => $prescription->patientRecord?->fullname ?? 'Patient',
                    'date' => optional($prescription->created_at)->toIso8601String(),
                ]))
            ->sortByDesc('date')
            ->slice($offset, 10)
            ->values();

        return response()->json(['items' => $activity, 'has_more' => $activity->count() === 10]);
    }

    private function profileUserData($user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'bio' => $user->bio,
            'license_no' => $user->license_no,
            'avatar_url' => $user->avatar_path ? asset('storage/'.$user->avatar_path) : null,
        ];
    }

    public function searchPatients(Request $request): JsonResponse
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:25'],
        ]);

        $patients = $this->patientService->searchPatients(
            $data['q'] ?? null,
            (int) ($data['limit'] ?? 12)
        );

        return response()->json(['patients' => $patients]);
    }

    public function showPatient(int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);

        return response()->json([
            'patient' => $this->patientService->patientToArray($patient),
        ]);
    }

    public function storePatient(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'birthday' => ['nullable', 'date'],
            'sex' => ['nullable', 'in:male,female,Male,Female'],
            'chief_complaint' => ['nullable', 'string'],
            'life_coach_id' => ['nullable', 'exists:users,id'],
        ]);

        $patient = $this->patientService->createPatient($data);
        if (! empty($data['life_coach_id'])) {
            $this->coachUpdateService->sync(User::findOrFail($data['life_coach_id']));
        }

        return response()->json([
            'message' => 'Patient added successfully.',
            'patient' => $this->patientService->patientToArray($patient->fresh('lifeCoach')),
        ], 201);
    }

    public function updatePatient(Request $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);

        $data = $request->validate([
            'fullname' => ['sometimes', 'required', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'religion' => ['nullable', 'string', 'max:255'],
            'sex' => ['nullable', 'in:male,female,Male,Female'],
            'gender' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['in:single,married,annulled,widowed,separated'],
            'student_year_level' => ['nullable', 'string', 'max:255'],
            'course' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'chief_complaint' => ['nullable', 'string'],
            'primary_diagnosis' => ['nullable', 'string', 'max:255'],
            'life_coach_id' => ['nullable', 'exists:users,id'],
        ]);

        $patient = $this->patientService->updatePatientRecord($patient, $data);
        if (! empty($data['life_coach_id'])) {
            $this->coachUpdateService->sync(User::findOrFail($data['life_coach_id']));
        }

        return response()->json([
            'message' => 'Patient record updated.',
            'patient' => $this->patientService->patientToArray($patient),
        ]);
    }

    public function updateMedicalHistory(UpdateMedicalHistoryRequest $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);
        $history = $this->patientService->updateMedicalHistory($patient, $request->validated());

        return response()->json([
            'message' => 'Medical history updated.',
            'medical_history' => $history,
        ]);
    }

    public function updatePsychiatricHistory(UpdatePsychiatricHistoryRequest $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);
        $history = $this->patientService->updatePsychiatricHistory($patient, $request->validated());

        return response()->json([
            'message' => 'Personal history updated.',
            'psychiatric_history' => $history,
        ]);
    }

    public function updateLifestyle(UpdateLifestyleAssessmentRequest $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);
        $assessment = $this->patientService->updateLifestyleAssessment($patient, $request->validated());

        return response()->json([
            'message' => 'Lifestyle assessment updated.',
            'lifestyle_assessment' => $assessment,
        ]);
    }

    public function updateSpiritualIntake(UpdateSpiritualIntakeRequest $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);
        $intake = $this->patientService->updateSpiritualIntake($patient, $request->validated());

        return response()->json([
            'message' => 'Spiritual intake updated.',
            'spiritual_intake' => $intake,
        ]);
    }

    public function storeConsultation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'patient_record_id' => ['required', 'exists:patient_records,id'],
            'date' => ['required', 'date'],
            'time' => ['required'],
            'type' => ['required', 'in:Initial,Follow-up,Emergency'],
            'notes' => ['nullable', 'string'],
        ]);

        $consultation = $this->patientService->createConsultation($data);

        return response()->json([
            'message' => 'Consultation scheduled.',
            'consultation' => $this->patientService->consultationToArray($consultation->load('patientRecord')),
        ], 201);
    }

    public function updateConsultation(Request $request, int $id): JsonResponse
    {
        $consultation = ConsultationSchedule::with('patientRecord')->findOrFail($id);

        $data = $request->validate([
            'date' => ['sometimes', 'required', 'date'],
            'time' => ['sometimes', 'required'],
            'type' => ['sometimes', 'required', 'in:Initial,Follow-up,Emergency'],
            'status' => ['sometimes', 'required', 'in:Scheduled,Completed,Cancelled'],
            'notes' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'],
        ]);

        $consultation = $this->patientService->updateConsultation($consultation, $data);

        return response()->json([
            'message' => 'Consultation updated.',
            'consultation' => $this->patientService->consultationToArray($consultation),
        ]);
    }

    public function destroyConsultation(int $id): JsonResponse
    {
        $consultation = ConsultationSchedule::findOrFail($id);
        $this->patientService->deleteConsultation($consultation);

        return response()->json(['message' => 'Consultation deleted.']);
    }

    public function showAssessment(int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);
        $assessment = $this->patientService->getAssessment($patient);

        return response()->json([
            'patient' => [
                'id' => $patient->id,
                'patient_id' => $patient->patient_id,
                'name' => $patient->fullname,
                'age' => $patient->age,
                'sex' => $patient->sex ? ucfirst($patient->sex) : null,
            ],
            'assessment' => $this->patientService->assessmentToArray($assessment),
            'updated_at' => optional($assessment?->updated_at)?->toIso8601String(),
        ]);
    }

    public function storeAssessment(Request $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);

        $payload = $request->validate([
            'status' => ['nullable', 'string', 'max:50'],
            'biological' => ['nullable', 'array'],
            'psychological' => ['nullable', 'array'],
            'social' => ['nullable', 'array'],
            'spiritual' => ['nullable', 'array'],
            'prayer_points' => ['nullable', 'array'],
            'intervention' => ['nullable', 'array'],
        ]);

        $assessment = $this->patientService->saveAssessment($patient, $payload);

        return response()->json([
            'message' => 'Assessment saved.',
            'assessment' => $this->patientService->assessmentToArray($assessment),
            'updated_at' => optional($assessment->updated_at)?->toIso8601String(),
        ]);
    }

    public function storePrescription(Request $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);
        $validated = $request->validate([
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'medications' => ['nullable', 'array'],
            'medications.*.name' => ['required', 'string', 'max:255'],
            'medications.*.dose' => ['nullable', 'string', 'max:100'],
            'medications.*.frequency' => ['nullable', 'string', 'max:100'],
            'medications.*.qty' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'string', 'in:Draft,Active,Completed,Cancelled'],
            'lifestyle_interventions' => ['nullable', 'array'],
            'lifestyle_interventions.*.category' => ['required', 'string', 'in:sleep,exercise,nutrition,stress,social,other'],
            'lifestyle_interventions.*.title' => ['required', 'string', 'max:255'],
            'lifestyle_interventions.*.target' => ['nullable', 'string', 'max:100'],
            'lifestyle_interventions.*.frequency' => ['nullable', 'string', 'max:100'],
            'lifestyle_interventions.*.duration' => ['nullable', 'string', 'max:100'],
            'lifestyle_interventions.*.instructions' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['medications'] = $validated['medications'] ?? [];
        $validated['lifestyle_interventions'] = $validated['lifestyle_interventions'] ?? [];

        $prescription = $this->patientService->savePrescription($patient, $validated);

        return response()->json([
            'message' => 'Prescription saved.',
            'prescription' => $prescription,
        ]);
    }

    public function storeClinicalTemplate(Request $request): JsonResponse
    {
        $data = $this->validateClinicalTemplate($request);
        $template = $this->patientService->createClinicalTemplate($data);

        return response()->json([
            'message' => 'Template saved.',
            'template' => $template->toArray(),
        ], 201);
    }

    public function updateClinicalTemplate(Request $request, int $id): JsonResponse
    {
        $template = ClinicalTemplate::findOrFail($id);
        $data = $this->validateClinicalTemplate($request, true);
        $template = $this->patientService->updateClinicalTemplate($template, $data);

        return response()->json([
            'message' => 'Template updated.',
            'template' => $template->toArray(),
        ]);
    }

    public function destroyClinicalTemplate(int $id): JsonResponse
    {
        $template = ClinicalTemplate::findOrFail($id);
        $this->patientService->deleteClinicalTemplate($template);

        return response()->json(['message' => 'Template deleted.']);
    }

    public function saveNote(Request $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);

        $patient->update([
            'clinical_notes' => $request->post('content'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'clinical notes updated!',
        ]);
    }

    public function uploadClinicalImage(Request $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);
        $image = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
        ])['image'];

        $path = $image->store("clinical-notes/{$patient->id}", 'public');
        $upload = $patient->clinicalUploads()->create([
            'path' => $path,
            'original_name' => $image->getClientOriginalName(),
            'size' => $image->getSize(),
        ]);

        return response()->json([
            'url' => asset('storage/'.$upload->path),
            'original_name' => $upload->original_name,
        ], 201);
    }

    private function validateClinicalTemplate(Request $request, bool $partial = false): array
    {
        $rules = [
            'type' => [$partial ? 'sometimes' : 'required', 'in:rx,dx'],
            'name' => [$partial ? 'sometimes' : 'required', 'string', 'max:255'],
            'tag' => ['nullable', 'string', 'max:100'],
            'tag_class' => ['nullable', 'string', 'max:100'],
            'tagClass' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'desc' => ['nullable', 'string', 'max:500'],
            'payload' => ['nullable', 'array'],
            'meds' => ['nullable', 'array'],
            'diag' => ['nullable', 'string', 'max:255'],
            'lifestyle' => ['nullable', 'array'],
            'lifestyle.*.category' => ['required', 'string', 'in:sleep,exercise,nutrition,stress,social,other'],
            'lifestyle.*.title' => ['required', 'string', 'max:255'],
            'lifestyle.*.target' => ['nullable', 'string', 'max:100'],
            'lifestyle.*.frequency' => ['nullable', 'string', 'max:100'],
            'lifestyle.*.duration' => ['nullable', 'string', 'max:100'],
            'lifestyle.*.instructions' => ['nullable', 'string', 'max:500'],
            'tests' => ['nullable', 'array'],
            'tests.*' => ['string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        return $request->validate($rules);
    }
}
