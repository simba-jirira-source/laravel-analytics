<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Tests;

abstract class DatabaseIntegrationTestCase extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function defineEnvironment($app): void
    {
        $driver = getenv('ANALYTICS_DB_DRIVER') ?: 'sqlite';

        match ($driver) {
            'mysql' => $this->configureMysql($app),
            'pgsql' => $this->configurePgsql($app),
            default => $this->configureSqlite($app),
        };
    }

    protected function configureSqlite($app): void
    {
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => 'file:laravel_analytics_integration?mode=memory&cache=shared',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }

    protected function configureMysql($app): void
    {
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'mysql',
            'host' => getenv('DB_HOST') ?: '127.0.0.1',
            'port' => getenv('DB_PORT') ?: '3306',
            'database' => getenv('DB_DATABASE') ?: 'laravel_analytics_test',
            'username' => getenv('DB_USERNAME') ?: 'root',
            'password' => getenv('DB_PASSWORD') ?: '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }

    protected function configurePgsql($app): void
    {
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'pgsql',
            'host' => getenv('DB_HOST') ?: '127.0.0.1',
            'port' => getenv('DB_PORT') ?: '5432',
            'database' => getenv('DB_DATABASE') ?: 'laravel_analytics_test',
            'username' => getenv('DB_USERNAME') ?: 'postgres',
            'password' => getenv('DB_PASSWORD') ?: 'postgres',
            'charset' => 'utf8',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }
}
