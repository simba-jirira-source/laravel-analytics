<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Tests;

abstract class MissingAuthorizationDashboardTestCase extends DatabaseTestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('analytics.dashboard.enabled', true);
        $app['config']->set('analytics.dashboard.authorization', null);
    }
}
