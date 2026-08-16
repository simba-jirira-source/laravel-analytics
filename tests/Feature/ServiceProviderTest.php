<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use LaravelAnalytics\LaravelAnalytics\LaravelAnalytics;

it('resolves the singleton', function () {
    expect(app(LaravelAnalytics::class))->toBeInstanceOf(LaravelAnalytics::class);
});

it('returns the same instance from the container', function () {
    expect(app(LaravelAnalytics::class))->toBe(app(LaravelAnalytics::class));
});

it('merges the package config', function () {
    expect(config('analytics.enabled'))->toBeFalse();
});

it('registers analytics artisan commands', function () {
    expect(Artisan::all())->toHaveKeys([
        'analytics:prune',
        'analytics:ip-ban',
        'analytics:ip-unban',
    ]);
});

it('registers publishable configuration', function () {
    $this->artisan('vendor:publish', [
        '--tag' => 'analytics-config',
        '--force' => true,
    ])->assertSuccessful();

    expect(config_path('analytics.php'))->toBeFile();
});

it('registers publishable migrations', function () {
    $this->artisan('vendor:publish', [
        '--tag' => 'analytics-migrations',
        '--force' => true,
    ])->assertSuccessful();

    expect(database_path('migrations'))->toBeDirectory();
});
