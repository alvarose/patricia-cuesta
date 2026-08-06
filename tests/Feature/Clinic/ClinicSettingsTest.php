<?php

use App\Domain\Booking\Slot\Support\BookingSettings;
use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Domain\Clinic\Settings\DTO\UpdateClinicProfileData;
use App\Domain\Clinic\Settings\UseCases\UpdateClinicProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->settings = app(SettingsServiceInterface::class);
});

function profileData(?UploadedFile $photo = null): UpdateClinicProfileData
{
    return new UpdateClinicProfileData(
        name: 'Patricia Cuesta',
        licenseNumber: 'M-12345',
        email: 'patricia@example.com',
        phone: '600111222',
        whatsapp: '34600111222',
        photo: $photo,
    );
}

it('guarda los datos del perfil', function () {
    app(UpdateClinicProfile::class)->execute(profileData());

    $profile = $this->settings->profile();

    expect($profile->whatsapp)->toBe('34600111222')
        ->and($profile->licenseNumber)->toBe('M-12345');
});

it('sustituye la foto y borra la anterior solo tras guardar', function () {
    app(UpdateClinicProfile::class)->execute(profileData(UploadedFile::fake()->image('antigua.jpg')));
    $previous = $this->settings->profile()->photoPath;

    Storage::disk('public')->assertExists($previous);

    app(UpdateClinicProfile::class)->execute(profileData(UploadedFile::fake()->image('nueva.jpg')));
    $current = $this->settings->profile()->photoPath;

    expect($current)->not->toBe($previous);
    Storage::disk('public')->assertExists($current);
    Storage::disk('public')->assertMissing($previous);
});

it('conserva la foto anterior si no se sube ninguna nueva', function () {
    app(UpdateClinicProfile::class)->execute(profileData(UploadedFile::fake()->image('foto.jpg')));
    $photo = $this->settings->profile()->photoPath;

    app(UpdateClinicProfile::class)->execute(profileData());

    expect($this->settings->profile()->photoPath)->toBe($photo);
    Storage::disk('public')->assertExists($photo);
});

it('ofrece un catálogo de opciones coherente con los valores por defecto', function (string $key) {
    expect(BookingSettings::OPTIONS[$key])->toContain(BookingSettings::defaults()[$key]);
})->with(array_keys(BookingSettings::OPTIONS));
