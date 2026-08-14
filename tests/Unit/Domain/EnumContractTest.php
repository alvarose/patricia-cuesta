<?php

use App\Domain\Booking\Appointment\Enums\AppointmentSource;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Contact\Message\Enums\ContactPreference;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\Patient\Enums\PatientStatus;
use App\Domain\Shared\Concerns\EnumHelpers;

/** @return list<class-string> */
function domainEnums(): array
{
    $root = str_replace('\\', '/', dirname(__DIR__, 3).'/app/Domain');
    $tree = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );

    $enums = [];

    foreach ($tree as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        $relative = substr(str_replace('\\', '/', $file->getPathname()), strlen($root) + 1);
        $class = 'App\\Domain\\'.str_replace('/', '\\', substr($relative, 0, -4));

        if (enum_exists($class)) {
            $enums[] = $class;
        }
    }

    sort($enums);

    return $enums;
}

it('encuentra los enums de dominio', function () {
    expect(domainEnums())->toContain(
        AppointmentSource::class,
        AppointmentStatus::class,
        ConsultationTopic::class,
        ContactPreference::class,
        PatientStatus::class,
    );
});

it('exige el trait EnumHelpers en todos ellos', function () {
    $missing = array_values(array_filter(
        domainEnums(),
        fn (string $enum): bool => ! in_array(EnumHelpers::class, class_uses($enum), true),
    ));

    expect($missing)->toBe([]);
});

it('devuelve los backings de un enum', function () {
    expect(AppointmentStatus::values())->toBe(['pending', 'confirmed', 'cancelled', 'completed'])
        ->and(AppointmentStatus::names())->toBe(['Pending', 'Confirmed', 'Cancelled', 'Completed'])
        ->and(AppointmentStatus::entries())->toBe(AppointmentStatus::cases());
});

it('agrupa comprobaciones de estado con isA e isNot', function () {
    expect(AppointmentStatus::Pending->isA(AppointmentStatus::blocking()))->toBeTrue()
        ->and(AppointmentStatus::Cancelled->isA(AppointmentStatus::blocking()))->toBeFalse()
        ->and(AppointmentStatus::Cancelled->isNot(AppointmentStatus::blocking()))->toBeTrue()
        ->and(AppointmentStatus::Confirmed->isA(AppointmentStatus::Confirmed))->toBeTrue();
});
