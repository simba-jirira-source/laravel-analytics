<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Tests;

abstract class DisabledDashboardTestCase extends DatabaseTestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('analytics.dashboard.enabled', false);
        $app['config']->set('analytics.dashboard.authorization', 'viewAnalyticsDashboard');
    }
}
