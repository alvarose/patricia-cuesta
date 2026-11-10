<?php

use Database\Seeders\AdminUserSeeder;

it('se niega a sembrar el administrador sin contraseña definida', function () {
    config()->set('clinic.admin_password', null);

    expect(fn () => $this->seed(AdminUserSeeder::class))
        ->toThrow(RuntimeException::class, 'Define ADMIN_PASSWORD');
});

it('no deja una contraseña por defecto en la configuración', function () {
    expect(config('clinic.admin_password'))->toBe(env('ADMIN_PASSWORD'));
});
