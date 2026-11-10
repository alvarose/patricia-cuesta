<?php

namespace App\Domain\Patients\PatientHistory\DTO;

use App\Domain\Core\Shared\Enums\ActionSource;
use App\Domain\Patients\PatientHistory\Enums\PatientHistoryField;
use App\Models\Patients\PatientHistory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

final readonly class RecordPatientHistoryData
{
    public function __construct(
        public int $patientId,
        public PatientHistoryField $field,
        public ?string $oldValue,
        public ?string $value,
        public ActionSource $source,
        public ?Model $actor = null,
        public ?string $description = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            PatientHistory::PATIENT_ID => $this->patientId,
            PatientHistory::FIELD => $this->field->value,
            PatientHistory::OLD_VALUE => $this->oldValue,
            PatientHistory::VALUE => $this->value,
            PatientHistory::SOURCE => $this->source,
            PatientHistory::ACTOR_TYPE => $this->actor?->getMorphClass(),
            PatientHistory::ACTOR_ID => $this->actor?->getKey(),
            PatientHistory::DESCRIPTION => $this->description,
            PatientHistory::EFFECTIVE_AT => CarbonImmutable::now(),
        ];
    }
}
