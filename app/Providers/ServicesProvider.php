<?php

namespace App\Providers;

use App\Domain\Booking\Absence\Contracts\AbsenceServiceInterface;
use App\Domain\Booking\Absence\Services\AbsenceService;
use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\Services\AppointmentService;
use App\Domain\Booking\Availability\Contracts\AvailabilityServiceInterface;
use App\Domain\Booking\Availability\Services\AvailabilityService;
use App\Domain\Booking\Slot\Contracts\SlotServiceInterface;
use App\Domain\Booking\Slot\Services\SlotService;
use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Domain\Clinic\Settings\Services\SettingsService;
use App\Domain\Contact\Message\Contracts\ContactMessageServiceInterface;
use App\Domain\Contact\Message\Services\ContactMessageService;
use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Domain\Patients\Patient\Services\PatientService;
use Illuminate\Support\ServiceProvider;

final class ServicesProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    private array $booking = [
        AppointmentServiceInterface::class => AppointmentService::class,
        AvailabilityServiceInterface::class => AvailabilityService::class,
        AbsenceServiceInterface::class => AbsenceService::class,
        SlotServiceInterface::class => SlotService::class,
    ];

    /** @var array<class-string, class-string> */
    private array $patients = [
        PatientServiceInterface::class => PatientService::class,
    ];

    /** @var array<class-string, class-string> */
    private array $contact = [
        ContactMessageServiceInterface::class => ContactMessageService::class,
    ];

    /** @var array<class-string, class-string> */
    private array $clinic = [
        SettingsServiceInterface::class => SettingsService::class,
    ];

    public function register(): void
    {
        $maps = [$this->booking, $this->patients, $this->contact, $this->clinic];

        foreach ($maps as $map) {
            foreach ($map as $interface => $implementation) {
                $this->app->bind($interface, $implementation);
            }
        }
    }
}
