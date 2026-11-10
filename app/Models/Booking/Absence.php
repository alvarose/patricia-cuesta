<?php

namespace App\Models\Booking;

use App\Http\Resources\Booking\Absence\AbsenceResource;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Database\Factories\Booking\AbsenceFactory;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property CarbonImmutable $starts_on
 * @property CarbonImmutable $ends_on
 * @property string|null $note
 */
#[UseResource(AbsenceResource::class)]
class Absence extends Model
{
    /** @use HasFactory<AbsenceFactory> */
    use HasFactory;

    const TABLE = 'absences';

    const ID = 'id';

    const STARTS_ON = 'starts_on';

    const ENDS_ON = 'ends_on';

    const NOTE = 'note';

    protected $table = self::TABLE;

    protected $guarded = [self::ID];

    protected function casts(): array
    {
        return [
            Absence::STARTS_ON => 'immutable_date',
            Absence::ENDS_ON => 'immutable_date',
        ];
    }

    public function covers(CarbonInterface $day): bool
    {
        return $day->betweenIncluded($this->starts_on, $this->ends_on);
    }

    public function isSingleDay(): bool
    {
        return $this->starts_on->equalTo($this->ends_on);
    }
}
