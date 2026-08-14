<?php

use App\Domain\Booking\Absence\Policies\AbsencePolicy;
use App\Domain\Booking\Appointment\Policies\AppointmentPolicy;
use App\Domain\Booking\Availability\Policies\AvailabilityPolicy;
use App\Domain\Clinic\Settings\Policies\SettingsPolicy;
use App\Domain\Contact\Message\Policies\ContactMessagePolicy;
use App\Domain\Patients\Patient\Policies\PatientPolicy;
use App\Models\Booking\Absence;
use App\Models\Booking\Appointment;
use App\Models\Booking\AvailabilityDay;
use App\Models\Clinic\Setting;
use App\Models\Contact\ContactMessage;
use App\Models\Patients\Patient;
use Illuminate\Support\Facades\Gate;

it('resuelve la policy de cada modelo protegido', function (string $model, string $policy) {
    expect(Gate::getPolicyFor($model))->toBeInstanceOf($policy);
})->with([
    [Appointment::class, AppointmentPolicy::class],
    [AvailabilityDay::class, AvailabilityPolicy::class],
    [Absence::class, AbsencePolicy::class],
    [Patient::class, PatientPolicy::class],
    [ContactMessage::class, ContactMessagePolicy::class],
    [Setting::class, SettingsPolicy::class],
]);
