<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use LaravelAnalytics\LaravelAnalytics\Models\PageView;
use LaravelAnalytics\LaravelAnalytics\Models\Visitor;

it('creates analytics tables', function () {
    expect(Schema::hasTable('analytics_visitors'))->toBeTrue()
        ->and(Schema::hasTable('analytics_page_views'))->toBeTrue()
        ->and(Schema::hasTable('analytics_errors'))->toBeTrue()
        ->and(Schema::hasTable('analytics_ip_bans'))->toBeTrue();
});

it('does not retain the skeleton placeholder table', function () {
    expect(Schema::hasTable('laravel_analytics_placeholder'))->toBeFalse();
});

it('supports foreign keys between visitors and page views', function () {
    $visitor = Visitor::factory()->create();
    $pageView = PageView::factory()->create([
        'visitor_id' => $visitor->id,
        'visitor_hash' => $visitor->visitor_hash,
    ]);

    expect($pageView->visitor?->is($visitor))->toBeTrue();
});
