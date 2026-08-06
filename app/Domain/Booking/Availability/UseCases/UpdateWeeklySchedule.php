<?php

namespace App\Domain\Booking\Availability\UseCases;

use App\Domain\Booking\Availability\Contracts\AvailabilityServiceInterface;
use App\Domain\Booking\Availability\DTO\UpdateWeeklyScheduleData;
use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use Illuminate\Support\Facades\DB;

final class UpdateWeeklySchedule
{
    public function __construct(
        private readonly AvailabilityServiceInterface $availability,
        private readonly SettingsServiceInterface $settings,
    ) {}

    public function execute(UpdateWeeklyScheduleData $data): void
    {
        DB::transaction(function () use ($data): void {
            $this->availability->replaceSchedule($data);
            $this->settings->updateBooking($data->settings);
        });
    }
}
