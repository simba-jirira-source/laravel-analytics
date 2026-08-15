<?php

declare(strict_types=1);

use LaravelAnalytics\LaravelAnalytics\LaravelAnalytics;

it('can be instantiated directly', function () {
    expect(new LaravelAnalytics)->toBeInstanceOf(LaravelAnalytics::class);
});

it('exposes a disabled-by-default enabled config value', function () {
    expect(config('analytics.enabled'))->toBeFalse();
});
