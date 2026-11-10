<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

final class MorphMapServiceProvider extends ServiceProvider
{
    /** @var array<string, class-string> */
    private const MORPHS = [
        'user' => User::class,
    ];

    public function boot(): void
    {
        Relation::enforceMorphMap(self::MORPHS);
    }
}
