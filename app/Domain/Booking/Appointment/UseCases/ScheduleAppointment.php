<?php

namespace App\Domain\Booking\Appointment\UseCases;

use App\Domain\Booking\Appointment\Contracts\AppointmentRepositoryInterface;
use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\DTO\CreateAppointmentData;
use App\Domain\Booking\Appointment\DTO\ScheduleAppointmentData;
use App\Domain\Booking\Appointment\Enums\AppointmentSource;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Mail\AppointmentConfirmed;
use App\Models\Booking\Appointment;
use App\Models\Patients\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

final class ScheduleAppointment
{
    public function __construct(
        private readonly AppointmentServiceInterface $appointments,
        private readonly AppointmentRepositoryInterface $repository,
        private readonly SettingsServiceInterface $settings,
    ) {}

    /** @throws ValidationException */
    public function execute(Patient $patient, ScheduleAppointmentData $data): Appointment
    {
        $endsAt = $this->settings->booking()->sessionEndFor($data->startsAt);

        $appointment = DB::transaction(function () use ($patient, $data, $endsAt): Appointment {
            if ($this->repository->lockBlockingOverlapping($data->startsAt, $endsAt)->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'time' => 'Ya tienes una cita a esa hora.',
                ]);
            }

            return $this->appointments->create(new CreateAppointmentData(
                contactName: $patient->fullName(),
                contactEmail: $data->email,
                contactPhone: $patient->phone,
                startsAt: $data->startsAt,
                endsAt: $endsAt,
                status: AppointmentStatus::Confirmed,
                source: AppointmentSource::Admin,
                topic: $patient->topic,
                patientId: $patient->id,
                notes: $data->notes,
            ));
        });

        Mail::to($appointment->contact_email)
            ->send((new AppointmentConfirmed($appointment))->afterCommit());

        return $appointment;
    }
}
