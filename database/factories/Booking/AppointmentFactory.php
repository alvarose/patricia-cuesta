<?php

namespace Database\Factories\Booking;

use App\Domain\Booking\Appointment\Enums\AppointmentSource;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Models\Booking\Appointment;
use App\Models\Patients\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends Factory<Appointment> */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $day = Carbon::today()->addDays(fake()->numberBetween(1, 21));

        while ($day->isWeekend()) {
            $day->addDay();
        }

        $startsAt = $day->copy()->setTime(fake()->randomElement([10, 11, 12, 16, 17, 18, 19]), 0);

        return [
            Appointment::PATIENT_ID => null,
            Appointment::CONTACT_NAME => fake()->name(),
            Appointment::CONTACT_EMAIL => fake()->safeEmail(),
            Appointment::CONTACT_PHONE => fake()->optional(0.8)->phoneNumber(),
            Appointment::TOPIC => fake()->randomElement(ConsultationTopic::cases()),
            Appointment::STARTS_AT => $startsAt,
            Appointment::ENDS_AT => $startsAt->copy()->addMinutes(50),
            Appointment::STATUS => AppointmentStatus::Confirmed,
            Appointment::SOURCE => AppointmentSource::Admin,
            Appointment::NOTES => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [Appointment::STATUS => AppointmentStatus::Pending]);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [Appointment::STATUS => AppointmentStatus::Confirmed]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [Appointment::STATUS => AppointmentStatus::Cancelled]);
    }

    public function completed(): static
    {
        return $this->state(function () {
            $day = Carbon::today()->subDays(fake()->numberBetween(1, 60));

            while ($day->isWeekend()) {
                $day->subDay();
            }

            $startsAt = $day->setTime(fake()->randomElement([10, 11, 12, 16, 17, 18]), 0);

            return [
                Appointment::STATUS => AppointmentStatus::Completed,
                Appointment::STARTS_AT => $startsAt,
                Appointment::ENDS_AT => $startsAt->copy()->addMinutes(50),
            ];
        });
    }

    public function web(): static
    {
        return $this->state(fn () => [Appointment::SOURCE => AppointmentSource::Web]);
    }

    public function forPatient(Patient $patient): static
    {
        return $this->state(fn () => [
            Appointment::PATIENT_ID => $patient->id,
            Appointment::CONTACT_NAME => $patient->fullName(),
            Appointment::CONTACT_EMAIL => $patient->email ?? fake()->safeEmail(),
            Appointment::CONTACT_PHONE => $patient->phone,
            Appointment::TOPIC => $patient->topic,
        ]);
    }
}
