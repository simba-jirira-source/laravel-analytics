<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Tests;

abstract class DatabaseTestCase extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => 'file:laravel_analytics_test?mode=memory&cache=shared',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }
}
