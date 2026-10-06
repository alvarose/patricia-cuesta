<?php

namespace App\Domain\Booking\Calendar\DTO;

final readonly class MonthCalendarData
{
    /** @param list<MonthDayData> $days */
    public function __construct(
        public string $label,
        public string $prev,
        public string $next,
        public string $today,
        public bool $isCurrentMonth,
        public array $days,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'prev' => $this->prev,
            'next' => $this->next,
            'today' => $this->today,
            'isCurrentMonth' => $this->isCurrentMonth,
            'days' => array_map(fn (MonthDayData $day): array => $day->toArray(), $this->days),
        ];
    }
}
