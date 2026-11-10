<?php

use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Booking\AppointmentHistory\Enums\AppointmentHistoryField;
use App\Domain\Contact\Message\Enums\ContactPreference;
use App\Domain\Core\Shared\Enums\ActionSource;
use App\Domain\Patients\PatientHistory\Enums\PatientHistoryField;
use App\Mail\AppointmentConfirmed;
use App\Mail\ContactMessageReply;
use App\Models\Booking\Appointment;
use App\Models\Booking\AppointmentHistory;
use App\Models\Contact\ContactMessage;
use App\Models\Patients\Patient;
use App\Models\Patients\PatientHistory;
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

it('cuenta el día con agenda aunque las sesiones estén completadas', function () {
    $day = CarbonImmutable::today();

    Appointment::factory()->create([
        Appointment::STATUS => AppointmentStatus::Completed,
        Appointment::STARTS_AT => $day->setTime(10, 0),
        Appointment::ENDS_AT => $day->setTime(10, 50),
    ]);

    $days = $this->get('/admin/citas')->viewData('page')['props']['month']['days'];

    expect(collect($days)->firstWhere('date', $day->toDateString())['sessions'])->toBe(1);
});

it('crea y actualiza un paciente por HTTP', function () {
    $this->post('/admin/pacientes', [
        'firstName' => 'Marta',
        'lastName' => 'Ruiz',
        'email' => 'marta@example.com',
    ])->assertRedirect();

    $patient = Patient::sole();

    $this->patch("/admin/pacientes/{$patient->id}", [
        'firstName' => 'Marta',
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

it('responde a un mensaje y avisa por email a quien lo pidio asi', function () {
    Mail::fake();

    $message = ContactMessage::factory()->create([
        ContactMessage::CONTACT_PREFERENCE => ContactPreference::Email,
        ContactMessage::EMAIL => 'lucia@example.com',
    ]);

    $this->post("/admin/mensajes/{$message->id}/respuesta", ['body' => 'Te escribo el lunes.'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($message->refresh()->replied_at)->not->toBeNull();
    Mail::assertQueued(
        ContactMessageReply::class,
        fn (ContactMessageReply $mail) => $mail->hasTo('lucia@example.com'),
    );
});

it('crea la ficha de paciente desde el mensaje y la vincula', function () {
    $message = ContactMessage::factory()->create([
        ContactMessage::NAME => 'Marta Ruiz',
        ContactMessage::EMAIL => 'marta@example.com',
    ]);

    $this->post("/admin/mensajes/{$message->id}/paciente")->assertRedirect();

    $patient = Patient::query()->sole();

    expect($message->refresh()->patient_id)->toBe($patient->id)
        ->and($patient->email)->toBe('marta@example.com');
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

it('anota en el historial de la cita quien cambio el estado desde el panel', function () {
    Mail::fake();

    $user = User::factory()->create();
    $appointment = Appointment::factory()->pending()->create();

    $this->actingAs($user)
        ->patch("/admin/citas/{$appointment->id}/estado", ['status' => 'cancelled'])
        ->assertRedirect();

    $row = AppointmentHistory::query()->sole();

    expect($row->field)->toBe(AppointmentHistoryField::Status->value)
        ->and($row->value)->toBe('cancelled')
        ->and($row->source)->toBe(ActionSource::Admin)
        ->and($row->actor_type)->toBe('user')
        ->and($row->actor_id)->toBe($user->id);
});

it('anota en el historial del paciente quien edito la ficha desde el panel', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create([Patient::NOTES => 'Primera sesión hecha.']);

    $this->actingAs($user)
        ->patch("/admin/pacientes/{$patient->id}", [
            'firstName' => $patient->first_name,
            'lastName' => $patient->last_name,
            'email' => $patient->email,
            'phone' => $patient->phone,
            'topic' => $patient->topic?->value,
            'notes' => 'Deriva a terapia de pareja.',
            'status' => $patient->status->value,
        ])
        ->assertRedirect();

    $row = PatientHistory::query()->sole();

    expect($row->field)->toBe(PatientHistoryField::Notes->value)
        ->and($row->old_value)->toBe('Primera sesión hecha.')
        ->and($row->value)->toBe('Deriva a terapia de pareja.')
        ->and($row->source)->toBe(ActionSource::Admin)
        ->and($row->actor_id)->toBe($user->id);
});
