<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Gate;
use SimbaJirira\LaravelAnalytics\Support\AllowAuthenticatedDashboardAccess;
use SimbaJirira\LaravelAnalytics\Support\DashboardAuthorizer;
use SimbaJirira\LaravelAnalytics\Tests\Support\DashboardUser;

it('denies dashboard access by default when authorization is not configured', function () {
    config([
        'analytics.dashboard.enabled' => true,
        'analytics.dashboard.authorization' => null,
    ]);

    expect(app(DashboardAuthorizer::class)->allowed(new DashboardUser))->toBeFalse();
});

it('allows access through a configured gate', function () {
    config([
        'analytics.dashboard.enabled' => true,
        'analytics.dashboard.authorization' => 'viewAnalyticsDashboard',
    ]);

    Gate::define('viewAnalyticsDashboard', fn () => true);

    expect(app(DashboardAuthorizer::class)->allowed(new DashboardUser))->toBeTrue();
});

it('allows access through an invokable authorization class', function () {
    config([
        'analytics.dashboard.enabled' => true,
        'analytics.dashboard.authorization' => AllowAuthenticatedDashboardAccess::class,
    ]);

    expect(app(DashboardAuthorizer::class)->allowed(new DashboardUser))->toBeTrue()
        ->and(app(DashboardAuthorizer::class)->allowed(null))->toBeFalse();
});
