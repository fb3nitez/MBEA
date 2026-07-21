<?php

namespace App\Http\Controllers;

use App\Models\ClinicalTemplate;
use App\Models\ConsultationSchedule;
use App\Models\PatientRecord;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PsychiatristController extends Controller
{
    public function __construct(private PatientService $patientService) {}

    public function dashboard(): View
    {
        $todayPatientsPage = $this->patientService->getPaginatedTodayPatients(10);
        $pendingConsultations = $this->patientService->getPendingConsultations();
        $highRiskPatients = $this->patientService->getHighRiskPatients();
        $allConsultations = $this->patientService->getConsultations();

        return view('psychiatrist.dashboard', [
            'patients' => $todayPatientsPage->getCollection(),
            'todayPatientsPaginator' => $todayPatientsPage->appends(request()->query()),
            'todayPatientsTotal' => $todayPatientsPage->total(),
            'pendingConsultations' => $pendingConsultations,
            'highRiskPatients' => $highRiskPatients,
            'pendingCount' => $allConsultations->where('status', 'Scheduled')->count(),
            'completedCount' => $allConsultations->where('status', 'Completed')->count(),
            'highRiskCount' => $highRiskPatients->count(),
            'lifeCoaches' => $this->patientService->getLifeCoaches(),
            'patientSuggestions' => $this->patientService->searchPatients(null, 12),
        ]);
    }

    public function patients(): View
    {
        $patientsPage = $this->patientService->getPaginatedPatients(10);
        $patients = $patientsPage->getCollection()
            ->map(fn (PatientRecord $p) => $this->patientService->patientToArray($p))
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
            ->map(fn ($c) => $this->patientService->consultationToArray($c))
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

        return view('psychiatrist.prescriptions', [
            'patientSuggestions' => $this->patientService->searchPatients(null, 12),
            'rxTemplates' => $templates->where('type', 'rx')->values(),
            'dxTemplates' => $templates->where('type', 'dx')->values(),
        ]);
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
            'marital_status' => ['nullable', 'in:single,married,annulled,widowed,separated'],
            'student_year_level' => ['nullable', 'string', 'max:255'],
            'course' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'chief_complaint' => ['nullable', 'string'],
            'primary_diagnosis' => ['nullable', 'string', 'max:255'],
            'clinical_notes' => ['nullable', 'string'],
            'life_coach_id' => ['nullable', 'exists:users,id'],
        ]);

        $patient = $this->patientService->updatePatientRecord($patient, $data);

        return response()->json([
            'message' => 'Patient record updated.',
            'patient' => $this->patientService->patientToArray($patient),
        ]);
    }

    public function updateMedicalHistory(Request $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);
        $history = $this->patientService->updateMedicalHistory($patient, $request->all());

        return response()->json([
            'message' => 'Medical history updated.',
            'medical_history' => $history,
        ]);
    }

    public function updatePsychiatricHistory(Request $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);
        $history = $this->patientService->updatePsychiatricHistory($patient, $request->all());

        return response()->json([
            'message' => 'Psychiatric history updated.',
            'psychiatric_history' => $history,
        ]);
    }

    public function updateLifestyle(Request $request, int $id): JsonResponse
    {
        $patient = $this->patientService->findPatient($id);
        $assessment = $this->patientService->updateLifestyleAssessment($patient, $request->all());

        return response()->json([
            'message' => 'Lifestyle assessment updated.',
            'lifestyle_assessment' => $assessment,
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
        $payload = $request->all();

        $prescription = $this->patientService->savePrescription($patient, $payload);

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
            'tests' => ['nullable', 'array'],
            'tests.*' => ['string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        return $request->validate($rules);
    }
}
