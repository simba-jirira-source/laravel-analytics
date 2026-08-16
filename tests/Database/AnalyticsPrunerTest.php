<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use LaravelAnalytics\LaravelAnalytics\Models\AnalyticsError;
use LaravelAnalytics\LaravelAnalytics\Models\IpBan;
use LaravelAnalytics\LaravelAnalytics\Models\PageView;
use LaravelAnalytics\LaravelAnalytics\Models\Visitor;
use LaravelAnalytics\LaravelAnalytics\Services\AnalyticsPruner;

beforeEach(function () {
    config([
        'analytics.retention.days' => 90,
        'analytics.retention.prune_page_views' => true,
        'analytics.retention.prune_visitors' => true,
        'analytics.retention.prune_errors' => true,
        'analytics.retention.prune_ip_bans' => true,
    ]);

    $this->pruner = app(AnalyticsPruner::class);
});

it('prunes page views older than the retention cutoff', function () {
    PageView::factory()->create(['viewed_at' => now()->subDays(120)]);
    PageView::factory()->create(['viewed_at' => now()->subDays(10)]);

    $results = $this->pruner->prune();

    expect($results['page_views'])->toBe(1)
        ->and(PageView::query()->count())->toBe(1);
});

it('prunes visitors without retained page views', function () {
    $staleVisitor = Visitor::factory()->create([
        'last_seen_at' => now()->subDays(120),
    ]);

    PageView::factory()->create([
        'visitor_id' => $staleVisitor->id,
        'visitor_hash' => $staleVisitor->visitor_hash,
        'viewed_at' => now()->subDays(120),
    ]);

    Visitor::factory()->create(['last_seen_at' => now()->subDays(10)]);

    $this->pruner->prune();

    expect(Visitor::query()->count())->toBe(1)
        ->and(PageView::query()->count())->toBe(0);
});

it('keeps visitors that still have page views inside retention', function () {
    $visitor = Visitor::factory()->create([
        'last_seen_at' => now()->subDays(120),
    ]);

    PageView::factory()->create([
        'visitor_id' => $visitor->id,
        'visitor_hash' => $visitor->visitor_hash,
        'viewed_at' => now()->subDays(10),
    ]);

    $this->pruner->prune();

    expect(Visitor::query()->count())->toBe(1)
        ->and(PageView::query()->count())->toBe(1);
});

it('prunes errors older than the retention cutoff', function () {
    AnalyticsError::factory()->create(['last_occurred_at' => now()->subDays(120)]);
    AnalyticsError::factory()->create(['last_occurred_at' => now()->subDays(5)]);

    $results = $this->pruner->prune();

    expect($results['errors'])->toBe(1)
        ->and(AnalyticsError::query()->count())->toBe(1);
});

it('deactivates expired ip bans and removes old ban records', function () {
    $expiredActive = IpBan::factory()->create([
        'ip_address' => '203.0.113.10',
        'expires_at' => now()->subDay(),
        'is_active' => true,
        'banned_at' => now()->subDays(10),
    ]);

    IpBan::factory()->inactive()->create([
        'ip_address' => '203.0.113.11',
        'banned_at' => now()->subDays(120),
    ]);

    IpBan::factory()->create([
        'ip_address' => '203.0.113.12',
        'banned_at' => now()->subDays(10),
    ]);

    $results = $this->pruner->prune();

    expect($results['deactivated_ip_bans'])->toBe(1)
        ->and($results['ip_bans'])->toBe(1)
        ->and(IpBan::query()->count())->toBe(2)
        ->and($expiredActive->fresh()?->is_active)->toBeFalse();
});

it('is idempotent when run repeatedly', function () {
    PageView::factory()->create(['viewed_at' => now()->subDays(120)]);
    AnalyticsError::factory()->create(['last_occurred_at' => now()->subDays(120)]);

    $first = $this->pruner->prune();
    $second = $this->pruner->prune();

    expect($first['page_views'])->toBe(1)
        ->and($first['errors'])->toBe(1)
        ->and($second['page_views'])->toBe(0)
        ->and($second['errors'])->toBe(0);
});

it('respects disabled prune toggles', function () {
    config([
        'analytics.retention.prune_page_views' => false,
        'analytics.retention.prune_visitors' => false,
        'analytics.retention.prune_errors' => false,
        'analytics.retention.prune_ip_bans' => false,
    ]);

    PageView::factory()->create(['viewed_at' => now()->subDays(120)]);
    AnalyticsError::factory()->create(['last_occurred_at' => now()->subDays(120)]);
    IpBan::factory()->expired()->create(['banned_at' => now()->subDays(120)]);

    $results = $this->pruner->prune();

    expect($results)->toBe([
        'page_views' => 0,
        'visitors' => 0,
        'errors' => 0,
        'deactivated_ip_bans' => 0,
        'ip_bans' => 0,
    ])->and(PageView::query()->count())->toBe(1)
        ->and(AnalyticsError::query()->count())->toBe(1)
        ->and(IpBan::query()->count())->toBe(1);
});

it('supports a custom retention override in days', function () {
    PageView::factory()->create(['viewed_at' => now()->subDays(20)]);
    PageView::factory()->create(['viewed_at' => now()->subDays(5)]);

    $results = $this->pruner->prune(10);

    expect($results['page_views'])->toBe(1)
        ->and(PageView::query()->count())->toBe(1);
});

it('never uses a future cutoff when days is zero', function () {
    PageView::factory()->create(['viewed_at' => now()->subHours(2)]);

    $cutoff = $this->pruner->resolveCutoff(0);

    expect($cutoff->lessThanOrEqualTo(Carbon::now()))->toBeTrue();
});
