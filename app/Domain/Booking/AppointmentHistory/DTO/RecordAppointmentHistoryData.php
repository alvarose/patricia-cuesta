<?php

namespace App\Domain\Booking\AppointmentHistory\DTO;

use App\Domain\Booking\AppointmentHistory\Enums\AppointmentHistoryField;
use App\Domain\Core\Shared\Enums\ActionSource;
use App\Models\Booking\AppointmentHistory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

final readonly class RecordAppointmentHistoryData
{
    public function __construct(
        public int $appointmentId,
        public AppointmentHistoryField $field,
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
            AppointmentHistory::APPOINTMENT_ID => $this->appointmentId,
            AppointmentHistory::FIELD => $this->field->value,
            AppointmentHistory::OLD_VALUE => $this->oldValue,
            AppointmentHistory::VALUE => $this->value,
            AppointmentHistory::SOURCE => $this->source,
            AppointmentHistory::ACTOR_TYPE => $this->actor?->getMorphClass(),
            AppointmentHistory::ACTOR_ID => $this->actor?->getKey(),
            AppointmentHistory::DESCRIPTION => $this->description,
            AppointmentHistory::EFFECTIVE_AT => CarbonImmutable::now(),
        ];
    }
}
