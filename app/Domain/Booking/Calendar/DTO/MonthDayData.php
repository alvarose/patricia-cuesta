<?php

namespace App\Domain\Booking\Calendar\DTO;

final readonly class MonthDayData
{
    public function __construct(
        public string $date,
        public int $num,
        public int $sessions,
        public bool $closed,
        public bool $absent,
        public bool $today,
        public bool $inMonth,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'num' => $this->num,
            'sessions' => $this->sessions,
            'closed' => $this->closed,
            'absent' => $this->absent,
            'today' => $this->today,
            'inMonth' => $this->inMonth,
        ];
    }
}
