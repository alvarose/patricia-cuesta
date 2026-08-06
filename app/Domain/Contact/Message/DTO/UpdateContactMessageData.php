<?php

namespace App\Domain\Contact\Message\DTO;

use App\Domain\Contact\Message\Enums\ContactPreference;
use App\Models\Contact\ContactMessage;
use Carbon\CarbonInterface;

final readonly class UpdateContactMessageData
{
    public function __construct(
        public ?CarbonInterface $readAt = null,
        public ?CarbonInterface $repliedAt = null,
        public ?ContactPreference $repliedVia = null,
        public ?int $patientId = null,
    ) {}

    public static function read(CarbonInterface $moment): self
    {
        return new self(readAt: $moment);
    }

    public static function replied(CarbonInterface $moment, ContactPreference $via): self
    {
        return new self(repliedAt: $moment, repliedVia: $via);
    }

    public static function patient(int $patientId): self
    {
        return new self(patientId: $patientId);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            ContactMessage::READ_AT => $this->readAt,
            ContactMessage::REPLIED_AT => $this->repliedAt,
            ContactMessage::REPLIED_VIA => $this->repliedVia?->value,
            ContactMessage::PATIENT_ID => $this->patientId,
        ], fn (mixed $value): bool => $value !== null);
    }
}
