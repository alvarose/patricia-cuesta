<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\JsonResource;

trait HasResource
{
    /** @return class-string<JsonResource> */
    public static function resourceClass(): string
    {
        return static::$resourceClass;
    }

    /** @param class-string<JsonResource>|null $resource */
    public function toModelResource(?string $resource = null): JsonResource
    {
        return $this->toResource($resource ?? static::resourceClass());
    }
}
