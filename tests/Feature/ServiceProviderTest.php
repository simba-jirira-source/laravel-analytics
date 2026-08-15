<?php

declare(strict_types=1);

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

it('loads the package translations', function () {
    expect(trans('analytics::messages.placeholder'))->toBe('Laravel Analytics placeholder translation.');
});

it('loads the package views', function () {
    expect(view()->exists('analytics::placeholder'))->toBeTrue();
});

it('registers the artisan command', function () {
    $this->artisan('analytics:placeholder')
        ->expectsOutputToContain('Laravel Analytics placeholder command executed.')
        ->assertSuccessful();
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
