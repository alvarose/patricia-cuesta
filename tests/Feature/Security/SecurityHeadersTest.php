<?php

use App\Models\User;
use App\Providers\AppServiceProvider;

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

it('no manda HSTS en local ni en los tests', function () {
    expect($this->get('/')->headers->has('Strict-Transport-Security'))->toBeFalse();
});

it('sí manda HSTS en los entornos desplegados', function (string $environment) {
    $this->app['env'] = $environment;

    expect($this->get('/')->headers->get('Strict-Transport-Security'))
        ->toContain('max-age=31536000');
})->with(['production', 'staging']);

it('fuerza HTTPS en los entornos desplegados y no en local', function (string $environment, string $scheme) {
    $this->app['env'] = $environment;
    (new AppServiceProvider($this->app))->boot();

    expect(url('/'))->toStartWith($scheme.'://');
})->with([
    ['production', 'https'],
    ['staging', 'https'],
    ['local', 'http'],
]);
