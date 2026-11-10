<?php

namespace App\Domain\Core\Shared\Enums;

use App\Domain\Core\Shared\Concerns\EnumHelpers;

enum ActionSource: string
{
    use EnumHelpers;

    case Admin = 'admin';
    case Web = 'web';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Desde el panel',
            self::Web => 'Desde la web',
            self::System => 'Automático',
        };
    }
}
