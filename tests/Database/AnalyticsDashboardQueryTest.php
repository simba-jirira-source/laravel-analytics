<?php

declare(strict_types=1);

use SimbaJirira\LaravelAnalytics\Models\AnalyticsError;
use SimbaJirira\LaravelAnalytics\Models\IpBan;
use SimbaJirira\LaravelAnalytics\Models\PageView;
use SimbaJirira\LaravelAnalytics\Models\Visitor;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use SimbaJirira\LaravelAnalytics\Support\DashboardDateRange;

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
        ->and($metrics['unique_visitors'])->toBe(1)
        ->and($metrics['visits'])->toBe(1)
        ->and($metrics['errors'])->toBe(1)
        ->and($metrics['active_bans'])->toBe(1);
});

it('counts visits as distinct visitor days', function () {
    $visitor = Visitor::factory()->create();

    PageView::factory()->create([
        'visitor_id' => $visitor->id,
        'visitor_hash' => $visitor->visitor_hash,
        'viewed_at' => now()->subDays(2),
    ]);
    PageView::factory()->create([
        'visitor_id' => $visitor->id,
        'visitor_hash' => $visitor->visitor_hash,
        'viewed_at' => now()->subDay(),
    ]);

    $metrics = $this->query->overviewMetrics($this->range);

    expect($metrics['unique_visitors'])->toBe(1)
        ->and($metrics['visits'])->toBe(2);
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
