<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\PoliciesProvider;
use App\Providers\RepositoryProvider;
use App\Providers\ServicesProvider;

return [
    AppServiceProvider::class,
    RepositoryProvider::class,
    ServicesProvider::class,
    PoliciesProvider::class,
    FortifyServiceProvider::class,
];
