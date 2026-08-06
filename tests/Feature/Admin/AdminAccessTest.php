<?php

use App\Models\Booking\Appointment;
use App\Models\Contact\ContactMessage;
use App\Models\Patients\Patient;
use App\Models\User;
use Database\Seeders\AvailabilitySeeder;

it('no existe el registro público', function () {
    $this->get('/register')->assertNotFound();
});

it('redirige a login a los invitados que van al panel', function () {
    $this->get('/admin')->assertRedirect('/login');
});

it('la web pública es accesible sin autenticación', function (string $path) {
    $this->get($path)->assertOk();
})->with(['/', '/aviso-legal', '/privacidad', '/cookies']);

it('todas las secciones del panel responden', function (string $path) {
    $this->seed(AvailabilitySeeder::class);
    Patient::factory()->count(2)->create();
    Appointment::factory()->count(2)->create();
    ContactMessage::factory()->count(2)->create();

    $this->actingAs(User::factory()->create());

    $this->get($path)->assertOk();
})->with([
    '/admin',
    '/admin/citas',
    '/admin/pacientes',
    '/admin/mensajes',
    '/admin/disponibilidad',
    '/admin/ajustes',
]);
