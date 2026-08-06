<?php

namespace App\Domain\Booking\Appointment\DTO;

use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Models\Booking\Appointment;

final readonly class UpdateAppointmentData
{
    public function __construct(
        public ?AppointmentStatus $status = null,
        public ?int $patientId = null,
        public ?string $notes = null,
    ) {}

    public static function status(AppointmentStatus $status): self
    {
        return new self(status: $status);
    }

    public static function patient(int $patientId): self
    {
        return new self(patientId: $patientId);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            Appointment::STATUS => $this->status,
            Appointment::PATIENT_ID => $this->patientId,
            Appointment::NOTES => $this->notes,
        ], fn (mixed $value): bool => $value !== null);
    }
}
