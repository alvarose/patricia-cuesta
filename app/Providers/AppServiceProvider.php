<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiting();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        URL::forceScheme(app()->isProduction() ? 'https' : 'http');

        JsonResource::withoutWrapping();

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('public-pages', fn (Request $request): Limit => Limit::perMinute(120)->by($request->ip()));

        RateLimiter::for('public-read', fn (Request $request): Limit => Limit::perMinute(60)->by($request->ip()));

        RateLimiter::for('public-write', fn (Request $request): Limit => Limit::perMinute(10)->by($request->ip()));

        RateLimiter::for('account-sensitive', fn (Request $request): Limit => Limit::perMinute(6)->by($request->user()?->id ?: $request->ip()));
    }
}
