<?php

use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Mail\AppointmentConfirmed;
use App\Models\Booking\Appointment;
use App\Models\Contact\ContactMessage;
use App\Models\Patients\Patient;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\AvailabilitySeeder;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(AvailabilitySeeder::class);
    $this->actingAs(User::factory()->create());
});

it('confirma una cita pendiente y avisa al cliente', function () {
    Mail::fake();

    $appointment = Appointment::factory()->web()->pending()->create([
        Appointment::CONTACT_EMAIL => 'cliente@example.com',
    ]);

    $this->patch("/admin/citas/{$appointment->id}/estado", ['status' => 'confirmed'])
        ->assertRedirect();

    expect($appointment->refresh()->status)->toBe(AppointmentStatus::Confirmed);
    Mail::assertQueued(AppointmentConfirmed::class);
});

it('devuelve 422 ante una transición imposible', function () {
    $appointment = Appointment::factory()->completed()->create();

    $this->patch("/admin/citas/{$appointment->id}/estado", ['status' => 'confirmed'])
        ->assertStatus(422);
});

it('crea la ficha de paciente desde la cita y la vincula', function () {
    $appointment = Appointment::factory()->web()->pending()->create([
        Appointment::CONTACT_NAME => 'Lucía Gómez',
        Appointment::CONTACT_EMAIL => 'lucia@example.com',
    ]);

    $this->post("/admin/citas/{$appointment->id}/paciente")->assertRedirect();

    $patient = Patient::sole();

    expect($appointment->refresh()->patient_id)->toBe($patient->id)
        ->and($patient->first_name)->toBe('Lucía');
});

it('marca el día con agenda aunque las sesiones estén completadas', function () {
    $monday = CarbonImmutable::today()->startOfWeek();

    Appointment::factory()->create([
        Appointment::STATUS => AppointmentStatus::Completed,
        Appointment::STARTS_AT => $monday->setTime(10, 0),
        Appointment::ENDS_AT => $monday->setTime(10, 50),
    ]);

    $week = $this->get('/admin/citas')->viewData('page')['props']['week'];

    expect(collect($week)->firstWhere('date', $monday->toDateString())['hasAppointments'])->toBeTrue();
});

it('crea y actualiza un paciente por HTTP', function () {
    $this->post('/admin/pacientes', [
        'first_name' => 'Marta',
        'last_name' => 'Ruiz',
        'email' => 'marta@example.com',
    ])->assertRedirect();

    $patient = Patient::sole();

    $this->patch("/admin/pacientes/{$patient->id}", [
        'first_name' => 'Marta',
        'status' => 'discharged',
    ])->assertRedirect();

    expect($patient->refresh()->status->value)->toBe('discharged');
});

it('abrir la bandeja no marca ningún mensaje como leído', function () {
    $message = ContactMessage::factory()->unread()->create();

    $this->get('/admin/mensajes')->assertOk();
    $this->get("/admin/mensajes?message={$message->id}")->assertOk();

    expect($message->refresh()->read_at)->toBeNull();
});

it('marca como leído solo al abrir el mensaje explícitamente', function () {
    $message = ContactMessage::factory()->unread()->create();

    $this->post("/admin/mensajes/{$message->id}/leido")
        ->assertRedirect("/admin/mensajes?message={$message->id}");

    expect($message->refresh()->read_at)->not->toBeNull();
});

it('rechaza franjas de disponibilidad solapadas', function () {
    $days = collect(range(1, 7))->map(fn (int $weekday) => [
        'weekday' => $weekday,
        'enabled' => $weekday === 1,
        'windows' => $weekday === 1
            ? [['start' => '10:00', 'end' => '14:00'], ['start' => '13:00', 'end' => '17:00']]
            : [],
    ])->all();

    $this->put('/admin/disponibilidad', [
        'days' => $days,
        'settings' => [
            'session_minutes' => 50,
            'buffer_minutes' => 10,
            'min_notice_hours' => 24,
            'max_advance_days' => 60,
        ],
    ])->assertSessionHasErrors('days');
});
