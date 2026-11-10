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
            'profile.license_number' => 'M-33233',
            'profile.email' => 'patriciacuestapsicologia@gmail.com',
            'profile.phone' => '',
            'profile.whatsapp' => '',
            'profile.photo_path' => null,
            ...$booking,
        ];
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        $fingerprint = self::fingerprint();
        $cached = Cache::get(self::CACHE_KEY);

        if (is_array($cached) && ($cached['fingerprint'] ?? null) === $fingerprint) {
            /** @var array<string, mixed> */
            return $cached['values'];
        }

        $values = array_merge(
            self::defaults(),
            Setting::query()->pluck(Setting::VALUE, Setting::KEY)->all(),
        );

        Cache::forever(self::CACHE_KEY, ['fingerprint' => $fingerprint, 'values' => $values]);

        return $values;
    }

    private static function fingerprint(): string
    {
        return md5(serialize(self::defaults()));
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
