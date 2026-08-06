<?php

namespace App\Domain\Booking\Appointment\UseCases;

use App\Domain\Booking\Appointment\Contracts\AppointmentRepositoryInterface;
use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\DTO\CreateAppointmentData;
use App\Domain\Booking\Appointment\Enums\AppointmentSource;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Booking\Slot\Contracts\SlotServiceInterface;
use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Mail\BookingRequested;
use App\Models\Booking\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

final class BookAppointment
{
    public function __construct(
        private readonly AppointmentServiceInterface $appointments,
        private readonly AppointmentRepositoryInterface $repository,
        private readonly SlotServiceInterface $slots,
        private readonly SettingsServiceInterface $settings,
    ) {}

    /**
     * @param  array{name: string, email: string, phone?: string|null}  $contact
     *
     * @throws ValidationException
     */
    public function execute(
        array $contact,
        CarbonImmutable $start,
        ?ConsultationTopic $topic = null,
    ): Appointment {
        $appointment = DB::transaction(function () use ($contact, $start, $topic): Appointment {
            $this->repository->lockBlockingOn($start);

            if (! $this->slots->isAvailable($start)) {
                throw ValidationException::withMessages([
                    'start' => 'Ese hueco acaba de ocuparse. Elige otra hora, por favor.',
                ]);
            }

            return $this->appointments->create(new CreateAppointmentData(
                contactName: $contact['name'],
                contactEmail: $contact['email'],
                contactPhone: $contact['phone'] ?? null,
                startsAt: $start,
                endsAt: $this->settings->booking()->sessionEndFor($start),
                status: AppointmentStatus::Pending,
                source: AppointmentSource::Web,
                topic: $topic,
            ));
        });

        Mail::to($this->settings->profile()->email)
            ->send((new BookingRequested($appointment))->afterCommit());

        return $appointment;
    }
}
