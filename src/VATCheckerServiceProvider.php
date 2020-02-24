<?php

namespace JairForo\VATChecker;

use Illuminate\Support\ServiceProvider;
use JairForo\VATChecker\Api\ApiGateway;
use JairForo\VATChecker\Api\ViesGateway;

class VATCheckerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ApiGateway::class, ViesGateway::class);
    }
}
