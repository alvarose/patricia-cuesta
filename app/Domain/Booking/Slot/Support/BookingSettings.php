<?php

namespace App\Domain\Booking\Slot\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final readonly class BookingSettings
{
    /** @var array<string, array<int>> */
    public const OPTIONS = [
        'session_minutes' => [30, 45, 50, 60],
        'buffer_minutes' => [0, 10, 15, 30],
        'min_notice_hours' => [0, 12, 24, 48],
        'max_advance_days' => [30, 60, 90],
    ];

    public function __construct(
        public int $sessionMinutes,
        public int $bufferMinutes,
        public int $minNoticeHours,
        public int $maxAdvanceDays,
    ) {}

    /** @return array<string, int> */
    public static function defaults(): array
    {
        return [
            'session_minutes' => 50,
            'buffer_minutes' => 10,
            'min_notice_hours' => 24,
            'max_advance_days' => 60,
        ];
    }

    public function slotStep(): int
    {
        return $this->sessionMinutes + $this->bufferMinutes;
    }

    public function sessionEndFor(CarbonImmutable $start): CarbonImmutable
    {
        return $start->addMinutes($this->sessionMinutes);
    }

    public function sessionRangeFor(CarbonImmutable $start): TimeRange
    {
        return new TimeRange($start, $this->sessionEndFor($start));
    }

    public function minNoticeAt(CarbonInterface $now): CarbonInterface
    {
        return $now->copy()->addHours($this->minNoticeHours);
    }

    public function horizonFrom(CarbonImmutable $today): CarbonImmutable
    {
        return $today->addDays($this->maxAdvanceDays);
    }

    /** @return array<string, int> */
    public function toArray(): array
    {
        return [
            'session_minutes' => $this->sessionMinutes,
            'buffer_minutes' => $this->bufferMinutes,
            'min_notice_hours' => $this->minNoticeHours,
            'max_advance_days' => $this->maxAdvanceDays,
        ];
    }
}
