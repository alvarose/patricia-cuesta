<?php

namespace App\Domain\Clinic\Settings\Contracts;

interface SettingsRepositoryInterface
{
    /** @return array<string, mixed> */
    public function all(): array;

    public function get(string $key, mixed $default = null): mixed;

    /** @param array<string, mixed> $values */
    public function setMany(array $values): void;

    public function flush(): void;
}
