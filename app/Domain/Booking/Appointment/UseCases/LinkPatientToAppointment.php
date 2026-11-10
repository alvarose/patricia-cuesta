<?php

namespace App\Domain\Booking\Appointment\UseCases;

use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\DTO\UpdateAppointmentData;
use App\Domain\Core\Shared\Enums\ActionSource;
use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Models\Booking\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class LinkPatientToAppointment
{
    public function __construct(
        private readonly AppointmentServiceInterface $appointments,
        private readonly PatientServiceInterface $patients,
    ) {}

    public function handle(
        Appointment $appointment,
        ActionSource $source,
        ?Model $actor = null,
    ): Appointment {
        if ($appointment->patient_id !== null) {
            return $appointment;
        }

        return DB::transaction(function () use ($appointment, $source, $actor): Appointment {
            $patient = $this->patients->findOrCreateFromContact(
                $appointment->contact_name,
                $appointment->contact_email,
                $appointment->contact_phone,
                $appointment->topic,
            );

            return $this->appointments->update(
                $appointment,
                UpdateAppointmentData::patient($patient->id),
                $source,
                $actor,
            );
        });
    }
}
