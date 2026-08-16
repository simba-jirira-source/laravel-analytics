<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;
use SimbaJirira\LaravelAnalytics\Models\PageView;
use SimbaJirira\LaravelAnalytics\Models\Visitor;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsPruner;
use SimbaJirira\LaravelAnalytics\Support\DashboardDateRange;

beforeEach(function () {
    $this->query = app(AnalyticsDashboardQuery::class);
    $this->range = DashboardDateRange::resolve(
        now()->subDays(7)->toDateString(),
        now()->toDateString(),
    );
});

it('caches overview metrics when cache ttl is configured', function () {
    config(['analytics.dashboard.cache_ttl' => 60]);

    Cache::flush();

    $visitor = Visitor::factory()->create();
    PageView::factory()->create([
        'visitor_id' => $visitor->id,
        'visitor_hash' => $visitor->visitor_hash,
        'viewed_at' => now()->subDay(),
    ]);

    $first = $this->query->overviewMetrics($this->range);

    PageView::factory()->create([
        'visitor_id' => $visitor->id,
        'visitor_hash' => $visitor->visitor_hash,
        'viewed_at' => now()->subDay(),
    ]);

    $second = $this->query->overviewMetrics($this->range);

    expect($first['page_views'])->toBe(1)
        ->and($second['page_views'])->toBe(1);
});

it('prunes page views in configured chunk sizes', function () {
    config([
        'analytics.retention.days' => 30,
        'analytics.retention.chunk_size' => 2,
    ]);

    PageView::factory()->count(5)->create(['viewed_at' => now()->subDays(120)]);

    $results = app(AnalyticsPruner::class)->prune();

    expect($results['page_views'])->toBe(5)
        ->and(PageView::query()->count())->toBe(0);
});
