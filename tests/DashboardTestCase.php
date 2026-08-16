<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Livewire\LivewireServiceProvider;
use SimbaJirira\LaravelAnalytics\AnalyticsServiceProvider;
use SimbaJirira\LaravelAnalytics\Tests\Support\DashboardUser;

abstract class DashboardTestCase extends DatabaseTestCase
{
    protected ?DashboardUser $authenticatedDashboardUser = null;

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            AnalyticsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('database.connections.testbench.database', ':memory:');

        $app['config']->set('auth.defaults.guard', 'web');
        $app['config']->set('auth.providers.users.model', DashboardUser::class);
        $app['config']->set('analytics.enabled', true);
        $app['config']->set('analytics.dashboard.enabled', true);
        $app['config']->set('analytics.dashboard.authorization', 'viewAnalyticsDashboard');
        $app['config']->set('analytics.dashboard.middleware', ['web']);
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('d', 32)));
    }

    protected function defineDatabaseMigrations(): void
    {
        parent::defineDatabaseMigrations();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        Gate::define('viewAnalyticsDashboard', fn (?DashboardUser $user = null): bool => $user !== null);
    }

    protected function dashboardUser(): DashboardUser
    {
        if ($this->authenticatedDashboardUser instanceof DashboardUser) {
            return $this->authenticatedDashboardUser;
        }

        return $this->authenticatedDashboardUser = DashboardUser::query()->create([
            'name' => 'Analytics Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
    }
}
