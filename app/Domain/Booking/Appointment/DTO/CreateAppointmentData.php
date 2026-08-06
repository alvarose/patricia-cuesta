<?php

namespace App\Domain\Booking\Appointment\DTO;

use App\Domain\Booking\Appointment\Enums\AppointmentSource;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Models\Booking\Appointment;
use Carbon\CarbonImmutable;

final readonly class CreateAppointmentData
{
    public function __construct(
        public string $contactName,
        public string $contactEmail,
        public ?string $contactPhone,
        public CarbonImmutable $startsAt,
        public CarbonImmutable $endsAt,
        public AppointmentStatus $status,
        public AppointmentSource $source,
        public ?ConsultationTopic $topic = null,
        public ?int $patientId = null,
        public ?string $notes = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            Appointment::CONTACT_NAME => $this->contactName,
            Appointment::CONTACT_EMAIL => $this->contactEmail,
            Appointment::CONTACT_PHONE => $this->contactPhone,
            Appointment::STARTS_AT => $this->startsAt,
            Appointment::ENDS_AT => $this->endsAt,
            Appointment::STATUS => $this->status,
            Appointment::SOURCE => $this->source,
            Appointment::TOPIC => $this->topic,
            Appointment::PATIENT_ID => $this->patientId,
            Appointment::NOTES => $this->notes,
        ];
    }
}
