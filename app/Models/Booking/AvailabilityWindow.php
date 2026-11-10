<?php

namespace App\Models\Booking;

use App\Domain\Booking\Slot\Support\TimeRange;
use App\Http\Resources\Booking\Availability\AvailabilityWindowResource;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $availability_day_id
 * @property string $start_time
 * @property string $end_time
 * @property int $position
 */
#[UseResource(AvailabilityWindowResource::class)]
class AvailabilityWindow extends Model
{
    const TABLE = 'availability_windows';

    const ID = 'id';

    const AVAILABILITY_DAY_ID = 'availability_day_id';

    const START_TIME = 'start_time';

    const END_TIME = 'end_time';

    const POSITION = 'position';

    protected $table = self::TABLE;

    protected $guarded = [self::ID];

    /** @return BelongsTo<AvailabilityDay, $this> */
    public function day(): BelongsTo
    {
        return $this->belongsTo(AvailabilityDay::class, self::AVAILABILITY_DAY_ID);
    }

    public function rangeOn(CarbonImmutable $day): TimeRange
    {
        return TimeRange::onDay($day, $this->start_time, $this->end_time);
    }

    public function startClock(): string
    {
        return substr($this->start_time, 0, 5);
    }

    public function endClock(): string
    {
        return substr($this->end_time, 0, 5);
    }
}
