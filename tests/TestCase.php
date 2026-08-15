<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Tests;

use LaravelAnalytics\LaravelAnalytics\LaravelAnalyticsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelAnalyticsServiceProvider::class,
        ];
    }
}
