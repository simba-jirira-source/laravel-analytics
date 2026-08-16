<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

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

it('does not register a facade alias', function () {
    expect(config('app.aliases', []))->not->toHaveKey('LaravelAnalytics');
});
