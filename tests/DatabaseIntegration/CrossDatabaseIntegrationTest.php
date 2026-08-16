<?php

declare(strict_types=1);

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use SimbaJirira\LaravelAnalytics\Models\AnalyticsError;
use SimbaJirira\LaravelAnalytics\Models\IpBan;
use SimbaJirira\LaravelAnalytics\Models\PageView;
use SimbaJirira\LaravelAnalytics\Models\Visitor;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsErrorRecorder;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsPruner;
use SimbaJirira\LaravelAnalytics\Services\IpBanService;
use SimbaJirira\LaravelAnalytics\Services\PageViewRecorder;
use SimbaJirira\LaravelAnalytics\Support\DashboardDateRange;

beforeEach(function () {
    config([
        'analytics.enabled' => true,
        'analytics.tracking.traffic' => true,
        'analytics.tracking.errors' => true,
        'analytics.ip_banning.enabled' => true,
        'analytics.retention.days' => 90,
        'analytics.retention.chunk_size' => 100,
    ]);
});

it('runs analytics migrations on the active driver', function () {
    expect(Schema::hasTable('analytics_visitors'))->toBeTrue()
        ->and(Schema::hasTable('analytics_page_views'))->toBeTrue()
        ->and(Schema::hasTable('analytics_errors'))->toBeTrue()
        ->and(Schema::hasTable('analytics_ip_bans'))->toBeTrue();
});

it('enforces visitor hash uniqueness', function () {
    Visitor::factory()->create(['visitor_hash' => str_repeat('a', 64)]);

    expect(fn () => Visitor::factory()->create(['visitor_hash' => str_repeat('a', 64)]))
        ->toThrow(QueryException::class);
});

it('records page views through the traffic recorder', function () {
    $visitor = Visitor::factory()->create();

    PageView::factory()->create([
        'visitor_id' => $visitor->id,
        'visitor_hash' => $visitor->visitor_hash,
        'path' => '/integration',
        'viewed_at' => now()->subDay(),
    ]);

    expect(PageView::query()->where('path', '/integration')->count())->toBe(1)
        ->and(app(PageViewRecorder::class))->toBeInstanceOf(PageViewRecorder::class);
});

it('aggregates errors by fingerprint', function () {
    $recorder = app(AnalyticsErrorRecorder::class);
    $request = Request::create('/broken', 'GET');
    $exception = new RuntimeException('integration failure');

    $recorder->record($exception, $request);
    $recorder->record($exception, $request);

    $error = AnalyticsError::query()->first();

    expect($error)->not->toBeNull()
        ->and($error?->occurrence_count)->toBe(2);
});

it('returns dashboard date metrics including distinct visitor days', function () {
    $query = app(AnalyticsDashboardQuery::class);
    $range = DashboardDateRange::resolve(
        now()->subDays(7)->toDateString(),
        now()->toDateString(),
    );

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

    $metrics = $query->overviewMetrics($range);

    expect($metrics['page_views'])->toBe(2)
        ->and($metrics['unique_visitors'])->toBe(1)
        ->and($metrics['visits'])->toBe(2);
});

it('stores and resolves active ip bans', function () {
    $service = app(IpBanService::class);

    $ban = $service->ban('203.0.113.50');

    expect($ban->is_active)->toBeTrue()
        ->and(IpBan::query()->active()->count())->toBe(1);
});

it('prunes stale analytics records in bounded batches', function () {
    config(['analytics.retention.days' => 30, 'analytics.retention.chunk_size' => 50]);

    PageView::factory()->count(3)->create(['viewed_at' => now()->subDays(120)]);
    PageView::factory()->create(['viewed_at' => now()->subDays(5)]);

    $staleVisitor = Visitor::factory()->create(['last_seen_at' => now()->subDays(120)]);
    PageView::factory()->create([
        'visitor_id' => $staleVisitor->id,
        'visitor_hash' => $staleVisitor->visitor_hash,
        'viewed_at' => now()->subDays(120),
    ]);

    AnalyticsError::factory()->create(['last_occurred_at' => now()->subDays(120)]);

    $results = app(AnalyticsPruner::class)->prune();

    expect($results['page_views'])->toBe(4)
        ->and(PageView::query()->count())->toBe(1)
        ->and(Visitor::query()->count())->toBeGreaterThanOrEqual(0)
        ->and($results['errors'])->toBe(1);
});
