<?php

arch()->preset()->php();

arch()->preset()->security();

arch()->preset()->laravel()
    ->ignoring([
        'App\Domain\Booking\Appointment\Enums',
        'App\Domain\Booking\AppointmentHistory\Enums',
        'App\Domain\Booking\Appointment\Exceptions',
        'App\Domain\Contact\Message\Enums',
        'App\Domain\Core\Shared\Enums',
        'App\Domain\Patients\Patient\Enums',
        'App\Domain\Patients\PatientHistory\Enums',
        'App\Http\Controllers\Settings\ProfileController',
        'App\Http\Controllers\Settings\SecurityController',
        'App\Models',
    ]);

arch('el dominio no conoce la capa HTTP')
    ->expect('App\Domain')
    ->not->toUse(['App\Http', 'App\Infrastructure']);

arch('los DTOs son finales y de solo lectura')
    ->expect('App\Domain\Booking\Appointment\DTO')
    ->toBeFinal()
    ->toBeReadonly()
    ->ignoring('App\Domain\Booking\Appointment\DTO\AppointmentFilterParams');

arch('los UseCases son finales')
    ->expect('App\Domain\Booking\Appointment\UseCases')
    ->toBeFinal();

arch('los repositorios son finales')
    ->expect('App\Domain\Patients\Patient\Repositories')
    ->toBeFinal();

arch('los controllers no tocan la base de datos')
    ->expect('App\Http\Controllers')
    ->not->toUse([
        'Illuminate\Support\Facades\DB',
        'Illuminate\Database\Eloquent\Builder',
    ]);

arch('los controllers no hablan con los repositorios')
    ->expect('App\Http\Controllers')
    ->not->toUse([
        'App\Domain\Booking\Appointment\Contracts\AppointmentRepositoryInterface',
        'App\Domain\Contact\Message\Contracts\ContactMessageRepositoryInterface',
        'App\Domain\Patients\Patient\Contracts\PatientRepositoryInterface',
    ]);

arch('los servicios no abren transacciones ni consultan Eloquent')
    ->expect('App\Domain')
    ->not->toUse('Illuminate\Support\Facades\DB')
    ->ignoring([
        'App\Domain\Booking\Appointment\UseCases',
        'App\Domain\Booking\Absence\UseCases',
        'App\Domain\Booking\Availability\UseCases',
        'App\Domain\Clinic\Settings\Repositories',
        'App\Domain\Clinic\Settings\UseCases',
        'App\Domain\Contact\Message\UseCases',
        'App\Domain\Patients\Patient\UseCases',
    ]);

arch('los modelos no invocan UseCases')
    ->expect('App\Models')
    ->not->toUse([
        'App\Domain\Booking\Appointment\UseCases',
        'App\Domain\Contact\Message\UseCases',
        'App\Domain\Patients\Patient\UseCases',
    ]);

arch('el historial de pacientes solo se usa desde su dominio')
    ->expect('App\Models\Patients\PatientHistory')
    ->toOnlyBeUsedIn('App\Domain\Patients\PatientHistory');

arch('el historial de citas solo se usa desde su dominio')
    ->expect('App\Models\Booking\AppointmentHistory')
    ->toOnlyBeUsedIn('App\Domain\Booking\AppointmentHistory');
