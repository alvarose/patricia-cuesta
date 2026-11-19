<?php

namespace App\Domain\Booking\Slot\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final readonly class BookingSettings
{
    /** @var array<string, string> */
    public const COLUMNS = [
        'sessionMinutes' => 'session_minutes',
        'bufferMinutes' => 'buffer_minutes',
        'minNoticeHours' => 'min_notice_hours',
        'maxAdvanceDays' => 'max_advance_days',
    ];

    /** @var array<string, array<int>> */
    public const OPTIONS = [
        'sessionMinutes' => [30, 45, 50, 60],
        'bufferMinutes' => [0, 10, 15, 30],
        'minNoticeHours' => [0, 12, 24, 48],
        'maxAdvanceDays' => [30, 60, 90],
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
            self::COLUMNS['sessionMinutes'] => 50,
            self::COLUMNS['bufferMinutes'] => 10,
            self::COLUMNS['minNoticeHours'] => 24,
            self::COLUMNS['maxAdvanceDays'] => 60,
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
            self::COLUMNS['sessionMinutes'] => $this->sessionMinutes,
            self::COLUMNS['bufferMinutes'] => $this->bufferMinutes,
            self::COLUMNS['minNoticeHours'] => $this->minNoticeHours,
            self::COLUMNS['maxAdvanceDays'] => $this->maxAdvanceDays,
        ];
    }
}
