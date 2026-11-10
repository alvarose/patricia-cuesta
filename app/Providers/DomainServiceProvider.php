<?php

namespace App\Providers;

use App\Domain\Booking\Absence\Contracts\AbsenceServiceInterface;
use App\Domain\Booking\Absence\Services\AbsenceService;
use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\Services\AppointmentService;
use App\Domain\Booking\AppointmentHistory\Contracts\AppointmentHistoryServiceInterface;
use App\Domain\Booking\AppointmentHistory\Services\AppointmentHistoryService;
use App\Domain\Booking\Availability\Contracts\AvailabilityServiceInterface;
use App\Domain\Booking\Availability\Services\AvailabilityService;
use App\Domain\Booking\Calendar\Contracts\MonthCalendarServiceInterface;
use App\Domain\Booking\Calendar\Services\MonthCalendarService;
use App\Domain\Booking\Slot\Contracts\SlotServiceInterface;
use App\Domain\Booking\Slot\Services\SlotService;
use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Domain\Clinic\Settings\Services\SettingsService;
use App\Domain\Contact\Message\Contracts\ContactMessageServiceInterface;
use App\Domain\Contact\Message\Services\ContactMessageService;
use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Domain\Patients\Patient\Services\PatientService;
use App\Domain\Patients\PatientHistory\Contracts\PatientHistoryServiceInterface;
use App\Domain\Patients\PatientHistory\Services\PatientHistoryService;
use Illuminate\Support\ServiceProvider;

final class DomainServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    private const BOOKING = [
        AppointmentServiceInterface::class => AppointmentService::class,
        AvailabilityServiceInterface::class => AvailabilityService::class,
        AbsenceServiceInterface::class => AbsenceService::class,
        SlotServiceInterface::class => SlotService::class,
        MonthCalendarServiceInterface::class => MonthCalendarService::class,
        AppointmentHistoryServiceInterface::class => AppointmentHistoryService::class,
    ];

    /** @var array<class-string, class-string> */
    private const PATIENTS = [
        PatientServiceInterface::class => PatientService::class,
        PatientHistoryServiceInterface::class => PatientHistoryService::class,
    ];

    /** @var array<class-string, class-string> */
    private const CONTACT = [
        ContactMessageServiceInterface::class => ContactMessageService::class,
    ];

    /** @var array<class-string, class-string> */
    private const CLINIC = [
        SettingsServiceInterface::class => SettingsService::class,
    ];

    public function register(): void
    {
        $maps = [self::BOOKING, self::PATIENTS, self::CONTACT, self::CLINIC];

        foreach ($maps as $map) {
            foreach ($map as $interface => $implementation) {
                $this->app->bind($interface, $implementation);
            }
        }
    }
}
