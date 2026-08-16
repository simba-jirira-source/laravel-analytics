<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use SimbaJirira\LaravelAnalytics\Models\AnalyticsError;
use SimbaJirira\LaravelAnalytics\Models\PageView;

it('runs the analytics prune command', function () {
    PageView::factory()->create(['viewed_at' => now()->subDays(120)]);
    AnalyticsError::factory()->create(['last_occurred_at' => now()->subDays(120)]);

    $exitCode = Artisan::call('analytics:prune');

    expect($exitCode)->toBe(0)
        ->and(PageView::query()->count())->toBe(0)
        ->and(AnalyticsError::query()->count())->toBe(0);
});

it('accepts a days override from the command line', function () {
    PageView::factory()->create(['viewed_at' => now()->subDays(20)]);

    Artisan::call('analytics:prune', ['--days' => '10']);

    expect(PageView::query()->count())->toBe(0);
});

it('rejects invalid days options', function () {
    $exitCode = Artisan::call('analytics:prune', ['--days' => 'not-a-number']);

    expect($exitCode)->toBe(1);
});

it('is safe to run repeatedly from the command line', function () {
    PageView::factory()->create(['viewed_at' => now()->subDays(120)]);

    Artisan::call('analytics:prune');
    $secondExitCode = Artisan::call('analytics:prune');

    expect($secondExitCode)->toBe(0)
        ->and(PageView::query()->count())->toBe(0);
});
