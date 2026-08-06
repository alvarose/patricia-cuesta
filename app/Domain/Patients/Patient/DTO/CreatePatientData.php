<?php

namespace App\Domain\Patients\Patient\DTO;

use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\Patient\Enums\PatientStatus;
use App\Models\Patients\Patient;

final readonly class CreatePatientData
{
    public function __construct(
        public string $firstName,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?ConsultationTopic $topic = null,
        public ?string $notes = null,
        public PatientStatus $status = PatientStatus::Active,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            Patient::FIRST_NAME => $this->firstName,
            Patient::LAST_NAME => $this->lastName,
            Patient::EMAIL => $this->email,
            Patient::PHONE => $this->phone,
            Patient::TOPIC => $this->topic,
            Patient::NOTES => $this->notes,
            Patient::STATUS => $this->status,
        ];
    }
}
