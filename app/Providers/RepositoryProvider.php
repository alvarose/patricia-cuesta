<?php

namespace App\Providers;

use App\Domain\Booking\Absence\Contracts\AbsenceRepositoryInterface;
use App\Domain\Booking\Absence\Repositories\AbsenceRepository;
use App\Domain\Booking\Appointment\Contracts\AppointmentRepositoryInterface;
use App\Domain\Booking\Appointment\Repositories\AppointmentRepository;
use App\Domain\Booking\Availability\Contracts\AvailabilityRepositoryInterface;
use App\Domain\Booking\Availability\Repositories\AvailabilityRepository;
use App\Domain\Clinic\Settings\Contracts\SettingsRepositoryInterface;
use App\Domain\Clinic\Settings\Repositories\SettingsRepository;
use App\Domain\Contact\Message\Contracts\ContactMessageRepositoryInterface;
use App\Domain\Contact\Message\Repositories\ContactMessageRepository;
use App\Domain\Patients\Patient\Contracts\PatientRepositoryInterface;
use App\Domain\Patients\Patient\Repositories\PatientRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    private array $booking = [
        AppointmentRepositoryInterface::class => AppointmentRepository::class,
        AvailabilityRepositoryInterface::class => AvailabilityRepository::class,
        AbsenceRepositoryInterface::class => AbsenceRepository::class,
    ];

    /** @var array<class-string, class-string> */
    private array $patients = [
        PatientRepositoryInterface::class => PatientRepository::class,
    ];

    /** @var array<class-string, class-string> */
    private array $contact = [
        ContactMessageRepositoryInterface::class => ContactMessageRepository::class,
    ];

    /** @var array<class-string, class-string> */
    private array $clinic = [
        SettingsRepositoryInterface::class => SettingsRepository::class,
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
