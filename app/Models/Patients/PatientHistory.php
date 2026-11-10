<?php

namespace App\Models\Patients;

use App\Http\Resources\Patients\PatientHistory\PatientHistoryResource;
use App\Models\Core\HistoryModel;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property int $patient_id */
#[UseResource(PatientHistoryResource::class)]
class PatientHistory extends HistoryModel
{
    const TABLE = 'patient_history';

    const PATIENT_ID = 'patient_id';

    protected $table = self::TABLE;

    /** @return BelongsTo<Patient, $this> */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, self::PATIENT_ID);
    }
}
