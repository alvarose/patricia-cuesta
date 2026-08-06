<?php

namespace App\Domain\Clinic\Settings\Repositories;

use App\Domain\Booking\Slot\Support\BookingSettings;
use App\Domain\Clinic\Settings\Contracts\SettingsRepositoryInterface;
use App\Models\Clinic\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class SettingsRepository implements SettingsRepositoryInterface
{
    private const CACHE_KEY = 'app-settings';

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        $booking = [];

        foreach (BookingSettings::defaults() as $key => $value) {
            $booking["booking.{$key}"] = $value;
        }

        return [
            'profile.name' => 'Patricia Cuesta',
            'profile.license_number' => 'M-XXXXX',
            'profile.email' => config('clinic.admin_email'),
            'profile.phone' => '',
            'profile.whatsapp' => '',
            'profile.photo_path' => null,
            ...$booking,
            'booking.calcom_url' => null,
        ];
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            return array_merge(
                self::defaults(),
                Setting::query()->pluck(Setting::VALUE, Setting::KEY)->all(),
            );
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default ?? (self::defaults()[$key] ?? null);
    }

    /** @param array<string, mixed> $values */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate([Setting::KEY => $key], [Setting::VALUE => $value]);
        }

        DB::afterCommit(fn () => $this->flush());
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
