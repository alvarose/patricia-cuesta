<?php

use App\Domain\Clinic\Settings\Contracts\SettingsRepositoryInterface;
use App\Models\Clinic\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::query()->where(Setting::KEY, 'booking.calcom_url')->delete();

        app(SettingsRepositoryInterface::class)->flush();
    }

    public function down(): void
    {
        //
    }
};
