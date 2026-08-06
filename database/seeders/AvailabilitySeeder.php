<?php

namespace Database\Seeders;

use App\Models\Booking\AvailabilityDay;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 7) as $weekday) {
            $day = AvailabilityDay::firstOrCreate(
                ['weekday' => $weekday],
                ['is_enabled' => $weekday <= 5],
            );

            if ($day->is_enabled && $day->windows()->count() === 0) {
                $day->windows()->createMany([
                    ['start_time' => '10:00', 'end_time' => '14:00', 'position' => 0],
                    ['start_time' => '16:00', 'end_time' => '20:00', 'position' => 1],
                ]);
            }
        }
    }
}
