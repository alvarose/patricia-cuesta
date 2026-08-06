<?php

namespace Database\Factories\Patients;

use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\Patient\Enums\PatientStatus;
use App\Models\Patients\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Patient> */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            Patient::FIRST_NAME => fake()->firstName(),
            Patient::LAST_NAME => fake()->lastName(),
            Patient::EMAIL => fake()->unique()->safeEmail(),
            Patient::PHONE => fake()->optional(0.8)->phoneNumber(),
            Patient::TOPIC => fake()->randomElement(ConsultationTopic::cases()),
            Patient::NOTES => fake()->optional(0.4)->sentence(10),
            Patient::STATUS => PatientStatus::Active,
        ];
    }

    public function paused(): static
    {
        return $this->state(fn () => [Patient::STATUS => PatientStatus::Paused]);
    }

    public function discharged(): static
    {
        return $this->state(fn () => [Patient::STATUS => PatientStatus::Discharged]);
    }
}
