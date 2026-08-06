<?php

namespace App\Http\Controllers\Web\Admin\Patient;

use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\Patient\Enums\PatientStatus;
use App\Domain\Patients\Patient\UseCases\CreatePatient;
use App\Domain\Patients\Patient\UseCases\DeletePatient;
use App\Domain\Patients\Patient\UseCases\UpdatePatient;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Patients\Patient\IndexPatientRequest;
use App\Http\Requests\Patients\Patient\StorePatientRequest;
use App\Http\Requests\Patients\Patient\UpdatePatientRequest;
use App\Http\Resources\Patients\Patient\PatientResource;
use App\Models\Patients\Patient;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PatientController extends WebController
{
    public function __construct(
        private readonly PatientServiceInterface $patients,
        private readonly CreatePatient $createPatient,
        private readonly UpdatePatient $updatePatient,
        private readonly DeletePatient $deletePatient,
    ) {}

    public function index(IndexPatientRequest $request): Response
    {
        $filters = $request->toData();

        return Inertia::render('admin/Patients', [
            'filters' => ['search' => $filters->search, 'status' => $filters->status],
            'topics' => ConsultationTopic::options(),
            'statuses' => PatientStatus::options(),
            'patients' => PatientResource::collection($this->patients->matching($filters)),
        ]);
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        $this->createPatient->execute($request->toData());

        return back();
    }

    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $this->updatePatient->execute($patient, $request->toData());

        return back();
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $this->deletePatient->execute($patient);

        return back();
    }
}
