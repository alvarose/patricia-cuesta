<?php

namespace App\Models\Core;

use App\Domain\Core\Shared\Enums\ActionSource;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $field
 * @property string|null $old_value
 * @property string|null $value
 * @property ActionSource $source
 * @property string|null $actor_type
 * @property int|null $actor_id
 * @property string|null $description
 * @property CarbonImmutable $effective_at
 */
abstract class HistoryModel extends Model
{
    const ID = 'id';

    const FIELD = 'field';

    const OLD_VALUE = 'old_value';

    const VALUE = 'value';

    const SOURCE = 'source';

    const ACTOR = 'actor';

    const ACTOR_TYPE = 'actor_type';

    const ACTOR_ID = 'actor_id';

    const DESCRIPTION = 'description';

    const EFFECTIVE_AT = 'effective_at';

    public $timestamps = false;

    protected $guarded = [self::ID];

    /** @return MorphTo<Model, $this> */
    public function actor(): MorphTo
    {
        return $this->morphTo(self::ACTOR);
    }

    protected function casts(): array
    {
        return [
            HistoryModel::SOURCE => ActionSource::class,
            HistoryModel::EFFECTIVE_AT => 'immutable_datetime',
        ];
    }
}
