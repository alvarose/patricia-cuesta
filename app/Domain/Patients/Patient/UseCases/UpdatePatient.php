<?php

namespace App\Domain\Patients\Patient\UseCases;

use App\Domain\Core\Shared\Enums\ActionSource;
use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Domain\Patients\Patient\DTO\UpdatePatientData;
use App\Models\Patients\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class UpdatePatient
{
    public function __construct(
        private readonly PatientServiceInterface $patients,
    ) {}

    public function handle(
        Patient $patient,
        UpdatePatientData $data,
        ActionSource $source,
        ?Model $actor = null,
    ): Patient {
        return DB::transaction(
            fn (): Patient => $this->patients->update($patient, $data, $source, $actor),
        );
    }
}
