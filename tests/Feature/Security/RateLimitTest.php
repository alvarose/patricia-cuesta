<?php

use Illuminate\Support\Facades\RateLimiter;

it('registra los limitadores con nombre', function (string $name) {
    expect(RateLimiter::limiter($name))->not->toBeNull();
})->with(['public-pages', 'public-read', 'public-write', 'account-sensitive']);

it('limita las rutas públicas que antes no tenían tope', function (string $uri) {
    $route = collect(app('router')->getRoutes()->getRoutes())
        ->first(fn ($candidate): bool => $candidate->uri() === $uri);

    expect($route)->not->toBeNull()
        ->and($route->gatherMiddleware())->toContain('throttle:public-pages');
})->with(['/', '{section}']);
