<?php

namespace App\Domain\Shared\Concerns;

use BackedEnum;

/** @phpstan-require-implements BackedEnum */
trait EnumHelpers
{
    /** @return list<self&BackedEnum> */
    abstract public static function cases(): array;

    /** @return list<string|int> */
    public static function values(): array
    {
        return array_map(static fn (BackedEnum $case) => $case->value, self::cases());
    }

    /** @return list<string> */
    public static function names(): array
    {
        return array_map(static fn (BackedEnum $case): string => $case->name, self::cases());
    }

    /** @return list<self> */
    public static function entries(): array
    {
        return self::cases();
    }

    /** @param  self|list<self>  $options */
    public function isA(self|array $options): bool
    {
        return in_array($this, is_array($options) ? $options : [$options], true);
    }

    /** @param  self|list<self>  $options */
    public function isNot(self|array $options): bool
    {
        return ! $this->isA($options);
    }
}
