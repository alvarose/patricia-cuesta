<?php

use App\Domain\Booking\Appointment\Enums\AppointmentSource;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Booking\Appointment\UseCases\BookAppointment;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Mail\BookingRequested;
use App\Models\Booking\Appointment;
use Carbon\CarbonImmutable;
use Database\Seeders\AvailabilitySeeder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed(AvailabilitySeeder::class);
    $this->book = app(BookAppointment::class);
    $this->slot = CarbonImmutable::today()->addDays(8)->next(CarbonImmutable::MONDAY)->setTime(10, 0);
    $this->contact = ['name' => 'Lucía Gómez', 'email' => 'lucia@example.com', 'phone' => '600111222'];
});

it('crea la cita pendiente con el snapshot de contacto y sin paciente', function () {
    Mail::fake();

    $appointment = $this->book->execute($this->contact, $this->slot, ConsultationTopic::Ansiedad);

    expect($appointment->status)->toBe(AppointmentStatus::Pending)
        ->and($appointment->source)->toBe(AppointmentSource::Web)
        ->and($appointment->patient_id)->toBeNull()
        ->and($appointment->contact_name)->toBe('Lucía Gómez')
        ->and($appointment->topic)->toBe(ConsultationTopic::Ansiedad);
});

it('deriva el fin de la sesión de la duración configurada', function () {
    Mail::fake();

    $appointment = $this->book->execute($this->contact, $this->slot);

    expect($appointment->starts_at->diffInMinutes($appointment->ends_at))->toBe(50.0);
});

it('avisa a Patricia de la nueva solicitud', function () {
    Mail::fake();

    $this->book->execute($this->contact, $this->slot);

    Mail::assertQueued(BookingRequested::class);
});

it('rechaza el hueco si se ocupó antes de guardar', function () {
    Mail::fake();

    Appointment::factory()->confirmed()->create([
        Appointment::STARTS_AT => $this->slot,
        Appointment::ENDS_AT => $this->slot->addMinutes(50),
    ]);

    expect(fn () => $this->book->execute($this->contact, $this->slot))
        ->toThrow(ValidationException::class);

    expect(Appointment::count())->toBe(1);
    Mail::assertNothingQueued();
});
