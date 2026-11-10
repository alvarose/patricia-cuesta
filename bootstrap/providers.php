<?php

use App\Providers\AppServiceProvider;
use App\Providers\DomainServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\MorphMapServiceProvider;
use App\Providers\PolicyServiceProvider;
use App\Providers\RepositoryServiceProvider;

return [
    AppServiceProvider::class,
    MorphMapServiceProvider::class,
    RepositoryServiceProvider::class,
    DomainServiceProvider::class,
    PolicyServiceProvider::class,
    FortifyServiceProvider::class,
];
