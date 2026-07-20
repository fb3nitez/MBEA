<?php

namespace App\Http\Controllers;

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
        $todayPatients = $this->patientService->getTodayPatients();
        $pendingConsultations = $this->patientService->getPendingConsultations();
        $highRiskPatients = $this->patientService->getHighRiskPatients();
        $allConsultations = $this->patientService->getConsultations();

        return view('psychiatrist.dashboard', [
            'patients' => $todayPatients,
            'pendingConsultations' => $pendingConsultations,
            'highRiskPatients' => $highRiskPatients,
            'pendingCount' => $allConsultations->where('status', 'Scheduled')->count(),
            'completedCount' => $allConsultations->where('status', 'Completed')->count(),
            'highRiskCount' => $highRiskPatients->count(),
            'lifeCoaches' => $this->patientService->getLifeCoaches(),
            'allPatients' => $this->patientService->getAllPatients()
                ->map(fn (PatientRecord $p) => [
                    'id' => $p->id,
                    'patient_id' => $p->patient_id,
                    'name' => $p->fullname,
                ])
                ->values(),
        ]);
    }

    public function patients(): View
    {
        $patients = $this->patientService->getAllPatients()
            ->map(fn (PatientRecord $p) => $this->patientService->patientToArray($p))
            ->values();

        return view('psychiatrist.patients', [
            'patients' => $patients,
            'lifeCoaches' => $this->patientService->getLifeCoaches(),
        ]);
    }

    public function consultations(): View
    {
        $consultations = $this->patientService->getConsultations()
            ->map(fn ($c) => $this->patientService->consultationToArray($c))
            ->values();

        $patients = $this->patientService->getAllPatients()
            ->map(fn (PatientRecord $p) => [
                'id' => $p->id,
                'patient_id' => $p->patient_id,
                'name' => $p->fullname,
            ])
            ->values();

        return view('psychiatrist.consultations', [
            'consultations' => $consultations,
            'patients' => $patients,
            'lifeCoaches' => $this->patientService->getLifeCoaches(),
        ]);
    }

    public function lifestyle(): View
    {
        $lifestylePatients = $this->patientService->getAllPatients()
            ->filter(fn (PatientRecord $p) => $p->lifestyleAssessment)
            ->map(fn (PatientRecord $p) => [
                'id' => $p->id,
                'patient_id' => $p->patient_id,
                'name' => $p->fullname,
                'age' => $p->age,
                'sex' => $p->sex,
                'lifestyle_assessment' => $p->lifestyleAssessment,
            ])
            ->values();

        return view('psychiatrist.lifestyle', [
            'lifestylePatients' => $lifestylePatients,
        ]);
    }

    public function assessments(): View
    {
        return view('psychiatrist.assessments');
    }

    public function prescriptions(): View
    {
        return view('psychiatrist.prescriptions');
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
            'status' => ['nullable', 'string', 'max:50'],
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
            'status' => ['nullable', 'string', 'max:50'],
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
}
