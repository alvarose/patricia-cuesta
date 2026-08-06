<?php

namespace Database\Factories\Booking;

use App\Models\Booking\Absence;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends Factory<Absence> */
class AbsenceFactory extends Factory
{
    protected $model = Absence::class;

    public function definition(): array
    {
        $start = Carbon::today()->addDays(fake()->numberBetween(5, 40));

        return [
            Absence::STARTS_ON => $start,
            Absence::ENDS_ON => $start->copy()->addDays(fake()->numberBetween(0, 7)),
            Absence::NOTE => fake()->optional(0.7)->randomElement(['Vacaciones', 'Formación', 'Congreso']),
        ];
    }
}
