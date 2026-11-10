<?php

namespace App\Providers;

use App\Domain\Booking\Absence\Policies\AbsencePolicy;
use App\Domain\Booking\Appointment\Policies\AppointmentPolicy;
use App\Domain\Booking\Availability\Policies\AvailabilityPolicy;
use App\Domain\Clinic\Settings\Policies\SettingsPolicy;
use App\Domain\Contact\Message\Policies\ContactMessagePolicy;
use App\Domain\Patients\Patient\Policies\PatientPolicy;
use App\Models\Booking\Absence;
use App\Models\Booking\Appointment;
use App\Models\Booking\AvailabilityDay;
use App\Models\Clinic\Setting;
use App\Models\Contact\ContactMessage;
use App\Models\Patients\Patient;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class PolicyServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    private const BOOKING = [
        Appointment::class => AppointmentPolicy::class,
        AvailabilityDay::class => AvailabilityPolicy::class,
        Absence::class => AbsencePolicy::class,
    ];

    /** @var array<class-string, class-string> */
    private const PATIENTS = [
        Patient::class => PatientPolicy::class,
    ];

    /** @var array<class-string, class-string> */
    private const CONTACT = [
        ContactMessage::class => ContactMessagePolicy::class,
    ];

    /** @var array<class-string, class-string> */
    private const CLINIC = [
        Setting::class => SettingsPolicy::class,
    ];

    public function boot(): void
    {
        $maps = [self::BOOKING, self::PATIENTS, self::CONTACT, self::CLINIC];

        foreach ($maps as $map) {
            foreach ($map as $model => $policy) {
                Gate::policy($model, $policy);
            }
        }
    }
}
