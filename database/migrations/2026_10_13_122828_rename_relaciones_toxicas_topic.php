<?php

use App\Models\Booking\Appointment;
use App\Models\Patients\Patient;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const OLD = 'relaciones_toxicas';

    private const NEW = 'vinculos_disfuncionales';

    public function up(): void
    {
        $this->rename(self::OLD, self::NEW);
    }

    public function down(): void
    {
        $this->rename(self::NEW, self::OLD);
    }

    private function rename(string $from, string $to): void
    {
        DB::transaction(function () use ($from, $to): void {
            DB::table(Patient::TABLE)->where(Patient::TOPIC, $from)->update([Patient::TOPIC => $to]);
            DB::table(Appointment::TABLE)->where(Appointment::TOPIC, $from)->update([Appointment::TOPIC => $to]);
        });
    }
};
