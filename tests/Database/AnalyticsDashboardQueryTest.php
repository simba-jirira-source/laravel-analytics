<?php

declare(strict_types=1);

use LaravelAnalytics\LaravelAnalytics\Models\AnalyticsError;
use LaravelAnalytics\LaravelAnalytics\Models\IpBan;
use LaravelAnalytics\LaravelAnalytics\Models\PageView;
use LaravelAnalytics\LaravelAnalytics\Models\Visitor;
use LaravelAnalytics\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use LaravelAnalytics\LaravelAnalytics\Support\DashboardDateRange;

beforeEach(function () {
    $this->query = app(AnalyticsDashboardQuery::class);
    $this->range = DashboardDateRange::resolve(
        now()->subDays(7)->toDateString(),
        now()->toDateString(),
    );
});

it('returns overview metrics for the selected range', function () {
    $visitor = Visitor::factory()->create();
    PageView::factory()->count(2)->create([
        'visitor_id' => $visitor->id,
        'visitor_hash' => $visitor->visitor_hash,
        'viewed_at' => now()->subDay(),
    ]);
    AnalyticsError::factory()->create(['last_occurred_at' => now()->subDay()]);
    IpBan::factory()->create(['ip_address' => '203.0.113.10']);

    $metrics = $this->query->overviewMetrics($this->range);

    expect($metrics['page_views'])->toBe(2)
        ->and($metrics['errors'])->toBe(1)
        ->and($metrics['active_bans'])->toBe(1);
});

it('returns ranked pages and referrers', function () {
    PageView::factory()->create([
        'path' => '/popular',
        'referrer_host' => 'example.com',
        'viewed_at' => now()->subDay(),
    ]);

    expect($this->query->topPages($this->range)->first()?->path)->toBe('/popular')
        ->and($this->query->topReferrers($this->range)->first()?->referrer_host)->toBe('example.com');
});
