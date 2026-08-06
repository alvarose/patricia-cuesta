<?php

namespace App\Models\Clinic;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $key
 * @property mixed $value
 */
class Setting extends Model
{
    const TABLE = 'settings';

    const ID = 'id';

    const KEY = 'key';

    const VALUE = 'value';

    protected $table = self::TABLE;

    protected $guarded = [self::ID];

    protected function casts(): array
    {
        return [
            self::VALUE => 'json',
        ];
    }
}
