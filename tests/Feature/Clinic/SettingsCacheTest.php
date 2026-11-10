<?php

use App\Domain\Clinic\Settings\Contracts\SettingsRepositoryInterface;
use App\Models\Clinic\Setting;
use Illuminate\Support\Facades\Cache;

it('sirve los ajustes desde la caché', function () {
    $repository = app(SettingsRepositoryInterface::class);

    expect($repository->all())->toBe($repository->all())
        ->and(Cache::has('app-settings'))->toBeTrue();
});

it('se da cuenta sola cuando cambian los valores por defecto', function () {
    $repository = app(SettingsRepositoryInterface::class);

    $repository->all();

    Cache::forever('app-settings', [
        'fingerprint' => 'de-otra-version-del-codigo',
        'values' => ['profile.license_number' => 'M-VIEJO'],
    ]);

    expect($repository->get('profile.license_number'))->not->toBe('M-VIEJO');
});

it('las filas guardadas siguen ganando al valor por defecto', function () {
    Setting::query()->create([
        Setting::KEY => 'profile.license_number',
        Setting::VALUE => 'M-00000',
    ]);

    app(SettingsRepositoryInterface::class)->flush();

    expect(app(SettingsRepositoryInterface::class)->get('profile.license_number'))->toBe('M-00000');
});
