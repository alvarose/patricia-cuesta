<?php

it('traduce las claves del framework que se ven en pantalla', function (string $key) {
    expect(trans($key))->not->toBe($key);
})->with([
    'auth.failed',
    'auth.throttle',
    'passwords.sent',
    'passwords.token',
    'validation.required',
    'validation.email',
    'validation.unique',
    'validation.accepted',
    'validation.date_format',
]);

it('traduce todas las reglas de validación del framework', function () {
    $crudas = collect(array_keys(require lang_path('es/validation.php')))
        ->reject(fn (string $key): bool => in_array($key, ['custom', 'attributes'], true))
        ->filter(fn (string $key): bool => ! is_array(trans("validation.{$key}")) && trans("validation.{$key}") === "validation.{$key}");

    expect($crudas->all())->toBe([]);
});

it('nombra en castellano los campos de los formularios', function (string $field) {
    expect(trans("validation.attributes.{$field}"))->not->toBe("validation.attributes.{$field}");
})->with([
    'firstName', 'lastName', 'email', 'phone', 'body', 'topic', 'privacy', 'date', 'time', 'notes',
    'contactPreference', 'preferredTime', 'startsOn', 'endsOn', 'licenseNumber',
]);

it('nombra los campos de los formularios en camelCase', function () {
    $snake = collect(array_keys(trans('validation.attributes')))
        ->filter(fn (string $field): bool => str_contains($field, '_'))
        ->reject(fn (string $field): bool => $field === 'current_password');

    expect($snake->all())->toBe([]);
});

it('cae al inglés del framework si falta una clave, nunca a la clave cruda', function () {
    expect(config('app.fallback_locale'))->toBe('en');
});
