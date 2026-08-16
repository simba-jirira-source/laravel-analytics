<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use SimbaJirira\LaravelAnalytics\Models\AnalyticsError;
use SimbaJirira\LaravelAnalytics\Models\IpBan;
use SimbaJirira\LaravelAnalytics\Models\PageView;
use SimbaJirira\LaravelAnalytics\Models\Visitor;

it('persists visitors with datetime casts', function () {
    $visitor = Visitor::factory()->create();

    expect($visitor->first_seen_at)->toBeInstanceOf(Carbon::class)
        ->and($visitor->last_seen_at)->toBeInstanceOf(Carbon::class)
        ->and($visitor->ip_address)->toBeNull();
});

it('persists page views with integer casts', function () {
    $pageView = PageView::factory()->create([
        'status_code' => 404,
        'duration_ms' => 120,
    ]);

    expect($pageView->status_code)->toBeInt()->toBe(404)
        ->and($pageView->duration_ms)->toBeInt()->toBe(120)
        ->and($pageView->viewed_at)->toBeInstanceOf(Carbon::class);
});

it('persists analytics errors with occurrence metadata', function () {
    $error = AnalyticsError::factory()->create([
        'occurrence_count' => 3,
    ]);

    expect($error->occurrence_count)->toBe(3)
        ->and($error->first_occurred_at)->toBeInstanceOf(Carbon::class)
        ->and($error->last_occurred_at)->toBeInstanceOf(Carbon::class);
});

it('persists ip bans with boolean and datetime casts', function () {
    $ban = IpBan::factory()->create([
        'is_active' => true,
        'expires_at' => now()->addDay(),
    ]);

    expect($ban->is_active)->toBeTrue()
        ->and($ban->banned_at)->toBeInstanceOf(Carbon::class)
        ->and($ban->expires_at)->toBeInstanceOf(Carbon::class);
});

it('supports inactive and expired ip ban factory states', function () {
    $inactive = IpBan::factory()->inactive()->create();
    $expired = IpBan::factory()->expired()->create();

    expect($inactive->is_active)->toBeFalse()
        ->and($expired->expires_at?->isPast())->toBeTrue();
});

it('relates visitors to page views', function () {
    $visitor = Visitor::factory()->create();
    PageView::factory()->count(2)->create([
        'visitor_id' => $visitor->id,
        'visitor_hash' => $visitor->visitor_hash,
    ]);

    expect($visitor->pageViews)->toHaveCount(2);
});
