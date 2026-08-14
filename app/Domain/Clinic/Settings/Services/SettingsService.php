<?php

namespace App\Domain\Clinic\Settings\Services;

use App\Domain\Booking\Slot\Support\BookingSettings;
use App\Domain\Clinic\Settings\Contracts\SettingsRepositoryInterface;
use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Domain\Clinic\Settings\DTO\UpdateClinicProfileData;
use App\Domain\Clinic\Settings\Support\ClinicProfile;

final class SettingsService implements SettingsServiceInterface
{
    public function __construct(
        private readonly SettingsRepositoryInterface $settings,
    ) {}

    public function profile(): ClinicProfile
    {
        return new ClinicProfile(
            name: (string) $this->settings->get('profile.name'),
            licenseNumber: (string) $this->settings->get('profile.license_number'),
            email: (string) $this->settings->get('profile.email'),
            phone: (string) $this->settings->get('profile.phone'),
            whatsapp: (string) $this->settings->get('profile.whatsapp'),
            photoPath: $this->settings->get('profile.photo_path'),
        );
    }

    public function booking(): BookingSettings
    {
        return new BookingSettings(
            sessionMinutes: (int) $this->settings->get('booking.session_minutes'),
            bufferMinutes: (int) $this->settings->get('booking.buffer_minutes'),
            minNoticeHours: (int) $this->settings->get('booking.min_notice_hours'),
            maxAdvanceDays: (int) $this->settings->get('booking.max_advance_days'),
        );
    }

    public function updateProfile(UpdateClinicProfileData $data, ?string $photoPath = null): void
    {
        $values = $data->toArray();

        if ($photoPath !== null) {
            $values['profile.photo_path'] = $photoPath;
        }

        $this->settings->setMany($values);
    }

    public function updateBooking(BookingSettings $settings): void
    {
        $values = [];

        foreach ($settings->toArray() as $key => $value) {
            $values["booking.{$key}"] = $value;
        }

        $this->settings->setMany($values);
    }
}
