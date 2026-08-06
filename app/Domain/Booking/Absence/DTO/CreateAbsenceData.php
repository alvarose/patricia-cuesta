<?php

namespace App\Domain\Booking\Absence\DTO;

use App\Models\Booking\Absence;
use Carbon\CarbonImmutable;

final readonly class CreateAbsenceData
{
    public function __construct(
        public CarbonImmutable $startsOn,
        public CarbonImmutable $endsOn,
        public ?string $note = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            Absence::STARTS_ON => $this->startsOn,
            Absence::ENDS_ON => $this->endsOn,
            Absence::NOTE => $this->note,
        ];
    }
}
