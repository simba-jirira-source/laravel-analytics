<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SimbaJirira\LaravelAnalytics\AnalyticsServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            AnalyticsServiceProvider::class,
        ];
    }
}
