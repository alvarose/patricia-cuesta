<?php

namespace Database\Seeders;

use App\Models\Booking\Absence;
use App\Models\Booking\Appointment;
use App\Models\Contact\ContactMessage;
use App\Models\Patients\Patient;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $patients = Patient::factory()->count(10)->create();
        Patient::factory()->paused()->create();
        Patient::factory()->discharged()->create();

        foreach ($patients as $patient) {
            Appointment::factory()
                ->count(fake()->numberBetween(1, 5))
                ->forPatient($patient)
                ->completed()
                ->create();
        }

        foreach ($patients->random(5) as $patient) {
            Appointment::factory()->forPatient($patient)->confirmed()->create();
        }

        Appointment::factory()->count(3)->web()->pending()->create();

        ContactMessage::factory()->count(5)->create();
        ContactMessage::factory()->count(3)->unread()->create();

        Absence::factory()->create(['note' => 'Vacaciones']);
    }
}
