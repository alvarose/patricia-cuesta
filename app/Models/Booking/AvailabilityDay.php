<?php

namespace App\Models\Booking;

use App\Http\Resources\Booking\Availability\AvailabilityDayResource;
use App\Traits\HasResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Resources\Json\JsonResource;
use UnexpectedValueException;

/**
 * @property int $id
 * @property int $weekday
 * @property bool $is_enabled
 * @property-read Collection<int, AvailabilityWindow> $windows
 */
class AvailabilityDay extends Model
{
    use HasResource;

    const TABLE = 'availability_days';

    const ID = 'id';

    const WEEKDAY = 'weekday';

    const IS_ENABLED = 'is_enabled';

    protected $table = self::TABLE;

    protected $guarded = [self::ID];

    /** @var class-string<JsonResource> */
    protected static string $resourceClass = AvailabilityDayResource::class;

    protected function casts(): array
    {
        return [
            self::WEEKDAY => 'integer',
            self::IS_ENABLED => 'boolean',
        ];
    }

    /** @return HasMany<AvailabilityWindow, $this> */
    public function windows(): HasMany
    {
        return $this->hasMany(AvailabilityWindow::class, AvailabilityWindow::AVAILABILITY_DAY_ID)
            ->orderBy(AvailabilityWindow::POSITION);
    }

    public function label(): string
    {
        return self::labelFor($this->weekday);
    }

    public static function labelFor(int $weekday): string
    {
        return match ($weekday) {
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
            default => throw new UnexpectedValueException("Día de la semana inválido: {$weekday}"),
        };
    }
}
