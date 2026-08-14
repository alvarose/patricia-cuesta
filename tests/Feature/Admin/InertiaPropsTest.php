<?php

use App\Models\Booking\Appointment;
use App\Models\Contact\ContactMessage;
use App\Models\Patients\Patient;
use App\Models\User;
use Database\Seeders\AvailabilitySeeder;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->seed(AvailabilitySeeder::class);
    $this->actingAs(User::factory()->create());
});

it('sirve las citas del día con todos sus campos y sin envolver', function () {
    $patient = Patient::factory()->create();
    Appointment::factory()->forPatient($patient)->confirmed()->create([
        Appointment::STARTS_AT => today()->setTime(10, 0),
        Appointment::ENDS_AT => today()->setTime(10, 50),
    ]);

    $this->get('/admin/citas')->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('admin/Appointments')
            ->has('appointments', 1)
            ->has('appointments.0', fn (AssertableInertia $item) => $item
                ->hasAll([
                    'id', 'time', 'name', 'topic', 'status', 'statusLabel', 'statusBadge',
                    'statusDot', 'hasPatient', 'email', 'phone',
                ])
                ->where('statusBadge', 'chip--conf')
                ->where('statusDot', 'dt--live'))
            ->has('week', 7),
    );
});

it('sirve el resumen con sus tarjetas y listas', function () {
    Appointment::factory()->confirmed()->create([
        Appointment::STARTS_AT => today()->setTime(11, 0),
        Appointment::ENDS_AT => today()->setTime(11, 50),
    ]);
    ContactMessage::factory()->unread()->create();

    $this->get('/admin')->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('admin/Dashboard')
            ->has('weekSummary')
            ->has('stats', 3)
            ->has('todayAppointments.0', fn (AssertableInertia $item) => $item->hasAll([
                'id', 'time', 'name', 'topic', 'status', 'statusLabel', 'statusBadge', 'statusDot',
            ]))
            ->has('recentMessages.0', fn (AssertableInertia $item) => $item->hasAll([
                'id', 'name', 'preview', 'when', 'unread', 'preference',
            ])),
    );
});

it('sirve los pacientes con su próxima cita', function () {
    $patient = Patient::factory()->create();
    Appointment::factory()->forPatient($patient)->confirmed()->create([
        Appointment::STARTS_AT => now()->addWeek()->setTime(10, 0),
        Appointment::ENDS_AT => now()->addWeek()->setTime(10, 50),
    ]);

    $this->get('/admin/pacientes')->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('admin/Patients')
            ->has('patients.0', fn (AssertableInertia $item) => $item
                ->hasAll([
                    'id', 'name', 'initials', 'since', 'topic', 'topicValue',
                    'sessions', 'next', 'status', 'statusLabel', 'statusBadge',
                    'email', 'phone', 'notes',
                ])
                ->where('sessions', 1)
                ->whereNot('next', null)),
    );
});

it('sirve la bandeja y el mensaje abierto', function () {
    ContactMessage::factory()->unread()->create();

    $this->get('/admin/mensajes')->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('admin/Messages')
            ->has('messages.0', fn (AssertableInertia $item) => $item->hasAll([
                'id', 'name', 'preview', 'when', 'unread', 'preference',
            ]))
            ->has('selected', fn (AssertableInertia $item) => $item->hasAll([
                'id', 'name', 'when', 'email', 'phone', 'preference',
                'preferredTime', 'body', 'replied', 'hasPatient',
            ])),
    );
});

it('sirve la disponibilidad con los 7 días y sus franjas', function () {
    $this->get('/admin/disponibilidad')->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('admin/Availability')
            ->has('days', 7)
            ->has('days.0', fn (AssertableInertia $day) => $day
                ->hasAll(['weekday', 'label', 'enabled', 'windows'])
                ->has('windows.0', fn (AssertableInertia $window) => $window->hasAll(['start', 'end'])))
            ->has('stats', 3)
            ->has('settings', fn (AssertableInertia $settings) => $settings->hasAll([
                'session_minutes', 'buffer_minutes', 'min_notice_hours', 'max_advance_days',
            ])),
    );
});

it('sirve el perfil de la consulta', function () {
    $this->get('/admin/ajustes')->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('admin/Settings')
            ->has('profile', fn (AssertableInertia $profile) => $profile->hasAll([
                'name', 'license_number', 'email', 'phone', 'whatsapp', 'photo_url',
            ]))
            ->missing('booking'),
    );
});

it('muestra el mismo extracto de un mensaje en el resumen y en la bandeja', function () {
    ContactMessage::factory()->create([
        ContactMessage::BODY => str_repeat('Necesito ayuda con la ansiedad que arrastro. ', 5),
    ]);

    $fromDashboard = $this->get('/admin')->viewData('page')['props']['recentMessages'][0]['preview'];
    $fromInbox = $this->get('/admin/mensajes')->viewData('page')['props']['messages'][0]['preview'];

    expect($fromDashboard)->toBe($fromInbox);
});
