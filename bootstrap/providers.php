<?php

use App\Providers\AppServiceProvider;
use App\Providers\ViewServiceProvider;
use Laravel\Wayfinder\WayfinderServiceProvider;

return [
    AppServiceProvider::class,
    ViewServiceProvider::class,
    WayfinderServiceProvider::class,
];
