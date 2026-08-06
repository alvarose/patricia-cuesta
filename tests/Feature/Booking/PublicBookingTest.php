<?php

use App\Domain\Booking\Appointment\Enums\AppointmentSource;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Mail\BookingRequested;
use App\Mail\ContactMessageReceived;
use App\Models\Booking\Appointment;
use App\Models\Contact\ContactMessage;
use Carbon\CarbonImmutable;
use Database\Seeders\AvailabilitySeeder;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(AvailabilitySeeder::class);
    $this->monday = CarbonImmutable::today()->addDays(8)->next(CarbonImmutable::MONDAY);
});

it('reserva una cita desde la web', function () {
    Mail::fake();

    $this->post('/reservas', [
        'name' => 'Lucía Gómez',
        'email' => 'lucia@example.com',
        'phone' => '600111222',
        'topic' => 'ansiedad',
        'start' => $this->monday->setTime(10, 0)->format('Y-m-d H:i'),
        'privacy' => true,
    ])->assertRedirect();

    $appointment = Appointment::sole();

    expect($appointment->status)->toBe(AppointmentStatus::Pending)
        ->and($appointment->source)->toBe(AppointmentSource::Web)
        ->and($appointment->patient_id)->toBeNull();

    Mail::assertQueued(BookingRequested::class);
});

it('rechaza un hueco ya ocupado', function () {
    Mail::fake();

    Appointment::factory()->confirmed()->create([
        Appointment::STARTS_AT => $this->monday->setTime(10, 0),
        Appointment::ENDS_AT => $this->monday->setTime(10, 50),
    ]);

    $this->post('/reservas', [
        'name' => 'Lucía Gómez',
        'email' => 'lucia@example.com',
        'start' => $this->monday->setTime(10, 0)->format('Y-m-d H:i'),
        'privacy' => true,
    ])->assertSessionHasErrors('start');

    expect(Appointment::query()->count())->toBe(1);
});

it('rechaza una reserva que no respeta la antelación mínima', function () {
    Mail::fake();

    $tomorrow = CarbonImmutable::tomorrow();

    while ($tomorrow->isWeekend()) {
        $tomorrow = $tomorrow->addDay();
    }

    $this->post('/reservas', [
        'name' => 'Lucía Gómez',
        'email' => 'lucia@example.com',
        'start' => $tomorrow->setTime(10, 0)->format('Y-m-d H:i'),
        'privacy' => true,
    ])->assertSessionHasErrors('start');

    expect(Appointment::query()->count())->toBe(0);
});

it('exige aceptar la política de privacidad', function () {
    $this->post('/reservas', [
        'name' => 'Lucía Gómez',
        'email' => 'lucia@example.com',
        'start' => $this->monday->setTime(10, 0)->format('Y-m-d H:i'),
    ])->assertSessionHasErrors('privacy');
});

it('expone los huecos como JSON hasta el horizonte configurado', function () {
    $from = CarbonImmutable::today();

    $response = $this->getJson('/reservas/slots?from='.$from->toDateString().'&to='.$from->addDays(89)->toDateString());

    $response->assertOk();

    expect(collect(array_keys($response->json()))->last())->toBe($from->addDays(60)->toDateString());
});

it('guarda el mensaje de contacto y avisa a Patricia', function () {
    Mail::fake();

    $this->post('/contacto', [
        'name' => 'Marta Ruiz',
        'email' => 'marta@example.com',
        'contact_preference' => 'whatsapp',
        'body' => 'Me gustaría empezar terapia.',
        'privacy' => true,
    ])->assertRedirect();

    expect(ContactMessage::sole()->name)->toBe('Marta Ruiz');
    Mail::assertQueued(ContactMessageReceived::class);
});

it('valida los campos obligatorios del contacto', function () {
    $this->post('/contacto', [])
        ->assertSessionHasErrors(['name', 'email', 'body', 'privacy', 'contact_preference']);
});
