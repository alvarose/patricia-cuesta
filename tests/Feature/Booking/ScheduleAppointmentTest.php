<?php

use App\Domain\Booking\Appointment\Enums\AppointmentSource;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Mail\AppointmentConfirmed;
use App\Models\Booking\Appointment;
use App\Models\Patients\Patient;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\AvailabilitySeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(AvailabilitySeeder::class);
    $this->actingAs(User::factory()->create());
    $this->monday = CarbonImmutable::today()->addDays(8)->next(CarbonImmutable::MONDAY);
    $this->patient = Patient::factory()->create([
        Patient::FIRST_NAME => 'Lucía',
        Patient::LAST_NAME => 'Gómez',
        Patient::EMAIL => 'lucia@example.com',
        Patient::PHONE => '600111222',
        Patient::TOPIC => ConsultationTopic::Ansiedad,
    ]);
});

function scheduleAt(Patient $patient, CarbonImmutable $start, array $overrides = []): array
{
    return array_merge([
        'date' => $start->toDateString(),
        'time' => $start->format('H:i'),
        'email' => $patient->email,
    ], $overrides);
}

it('crea la cita confirmada con los datos de la ficha', function () {
    Mail::fake();

    $start = $this->monday->setTime(10, 0);

    $this->post("/admin/pacientes/{$this->patient->id}/citas", scheduleAt($this->patient, $start, [
        'notes' => 'Primera sesión de seguimiento.',
    ]))->assertRedirect();

    $appointment = Appointment::sole();

    expect($appointment->status)->toBe(AppointmentStatus::Confirmed)
        ->and($appointment->source)->toBe(AppointmentSource::Admin)
        ->and($appointment->patient_id)->toBe($this->patient->id)
        ->and($appointment->contact_name)->toBe('Lucía Gómez')
        ->and($appointment->contact_phone)->toBe('600111222')
        ->and($appointment->topic)->toBe(ConsultationTopic::Ansiedad)
        ->and($appointment->notes)->toBe('Primera sesión de seguimiento.')
        ->and($appointment->starts_at->format('Y-m-d H:i'))->toBe($start->format('Y-m-d H:i'))
        ->and($appointment->ends_at->format('H:i'))->toBe('10:50');
});

it('avisa al paciente por correo', function () {
    Mail::fake();

    $this->post(
        "/admin/pacientes/{$this->patient->id}/citas",
        scheduleAt($this->patient, $this->monday->setTime(10, 0)),
    )->assertRedirect();

    Mail::assertQueued(
        AppointmentConfirmed::class,
        fn (AppointmentConfirmed $mail): bool => $mail->hasTo('lucia@example.com'),
    );
});

it('cita fuera del horario publicado', function () {
    Mail::fake();

    $saturday = $this->monday->addDays(5)->setTime(9, 15);

    $this->post(
        "/admin/pacientes/{$this->patient->id}/citas",
        scheduleAt($this->patient, $saturday),
    )->assertRedirect();

    expect(Appointment::query()->count())->toBe(1)
        ->and(Appointment::sole()->starts_at->format('Y-m-d H:i'))->toBe($saturday->format('Y-m-d H:i'));
});

it('cita dentro de la antelación mínima', function () {
    Mail::fake();

    $this->travelTo($this->monday->subDay()->setTime(12, 0));

    $this->post(
        "/admin/pacientes/{$this->patient->id}/citas",
        scheduleAt($this->patient, $this->monday->setTime(10, 0)),
    )->assertRedirect();

    expect(Appointment::query()->count())->toBe(1);
});

it('rechaza una cita que solapa con otra bloqueante', function () {
    Mail::fake();

    Appointment::factory()->confirmed()->create([
        Appointment::STARTS_AT => $this->monday->setTime(10, 0),
        Appointment::ENDS_AT => $this->monday->setTime(10, 50),
    ]);

    $this->post(
        "/admin/pacientes/{$this->patient->id}/citas",
        scheduleAt($this->patient, $this->monday->setTime(10, 30)),
    )->assertSessionHasErrors('time');

    expect(Appointment::query()->count())->toBe(1);
    Mail::assertNothingQueued();
});

it('acepta una sesión pegada a la anterior', function () {
    Mail::fake();

    Appointment::factory()->confirmed()->create([
        Appointment::STARTS_AT => $this->monday->setTime(10, 0),
        Appointment::ENDS_AT => $this->monday->setTime(10, 50),
    ]);

    $this->post(
        "/admin/pacientes/{$this->patient->id}/citas",
        scheduleAt($this->patient, $this->monday->setTime(10, 50)),
    )->assertRedirect();

    expect(Appointment::query()->count())->toBe(2);
});

it('no se estorba con una cita cancelada', function () {
    Mail::fake();

    Appointment::factory()->cancelled()->create([
        Appointment::STARTS_AT => $this->monday->setTime(10, 0),
        Appointment::ENDS_AT => $this->monday->setTime(10, 50),
    ]);

    $this->post(
        "/admin/pacientes/{$this->patient->id}/citas",
        scheduleAt($this->patient, $this->monday->setTime(10, 0)),
    )->assertRedirect();

    expect(Appointment::query()->count())->toBe(2);
});

it('exige día, hora y correo', function () {
    $this->post("/admin/pacientes/{$this->patient->id}/citas", [])
        ->assertSessionHasErrors(['date', 'time', 'email']);

    expect(Appointment::query()->count())->toBe(0);
});

it('redirige a login a los invitados', function () {
    Auth::logout();

    $this->post(
        "/admin/pacientes/{$this->patient->id}/citas",
        scheduleAt($this->patient, $this->monday->setTime(10, 0)),
    )->assertRedirect('/login');

    expect(Appointment::query()->count())->toBe(0);
});
