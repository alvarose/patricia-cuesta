<?php

use App\Models\User;

it('añade las cabeceras de seguridad a las respuestas', function (string $header, string $value) {
    $this->get('/')->assertHeader($header, $value);
})->with([
    ['X-Content-Type-Options', 'nosniff'],
    ['X-Frame-Options', 'SAMEORIGIN'],
    ['Referrer-Policy', 'strict-origin-when-cross-origin'],
]);

it('declara la política de permisos', function () {
    $permissions = $this->get('/')->headers->get('Permissions-Policy');

    expect($permissions)->toContain('camera=()')
        ->and($permissions)->toContain('microphone=()')
        ->and($permissions)->toContain('geolocation=()');
});

it('también las pone en el panel', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/admin')->assertHeader('X-Content-Type-Options', 'nosniff');
});

it('no manda HSTS fuera de producción', function () {
    expect($this->get('/')->headers->has('Strict-Transport-Security'))->toBeFalse();
});
