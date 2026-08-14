<?php

namespace App\Domain\Clinic\Settings\Contracts;

use App\Domain\Booking\Slot\Support\BookingSettings;
use App\Domain\Clinic\Settings\DTO\UpdateClinicProfileData;
use App\Domain\Clinic\Settings\Support\ClinicProfile;

interface SettingsServiceInterface
{
    public function profile(): ClinicProfile;

    public function booking(): BookingSettings;

    public function updateProfile(UpdateClinicProfileData $data, ?string $photoPath = null): void;

    public function updateBooking(BookingSettings $settings): void;
}
