<?php

declare(strict_types=1);

use LaravelAnalytics\LaravelAnalytics\Models\PageView;
use LaravelAnalytics\LaravelAnalytics\Models\Visitor;
use LaravelAnalytics\LaravelAnalytics\Services\VisitorAnalytics;

it('counts a repeat visitor after multiple tracked page views', function () {
    $this->get('/test-page')->assertOk();
    $this->get('/test-page')->assertOk();

    expect(Visitor::query()->count())->toBe(1)
        ->and(PageView::query()->count())->toBe(2);

    $visitor = Visitor::query()->first();

    expect(app(VisitorAnalytics::class)->isRepeatVisitor($visitor))->toBeTrue()
        ->and(app(VisitorAnalytics::class)->repeatVisitorCount())->toBe(1)
        ->and(app(VisitorAnalytics::class)->uniqueVisitorCount())->toBe(1);
});

it('creates separate unique visitors for different client fingerprints', function () {
    $this->withServerVariables([
        'REMOTE_ADDR' => '198.51.100.50',
        'HTTP_USER_AGENT' => 'VisitorTest/1.0',
    ])->get('/test-page')->assertOk();

    $this->withServerVariables([
        'REMOTE_ADDR' => '198.51.100.51',
        'HTTP_USER_AGENT' => 'VisitorTest/1.0',
    ])->get('/test-page')->assertOk();

    expect(app(VisitorAnalytics::class)->uniqueVisitorCount())->toBe(2)
        ->and(app(VisitorAnalytics::class)->repeatVisitorCount())->toBe(0);
});

it('omits raw ip addresses from visitor records by default', function () {
    $this->withServerVariables([
        'REMOTE_ADDR' => '198.51.100.60',
        'HTTP_USER_AGENT' => 'VisitorTest/1.0',
    ])->get('/test-page')->assertOk();

    expect(Visitor::query()->first()?->ip_address)->toBeNull()
        ->and(Visitor::query()->first()?->ip_hash)->not->toBeNull();
});

it('stores raw ip addresses when configured', function () {
    config(['analytics.privacy.store_raw_ip' => true]);

    $this->withServerVariables([
        'REMOTE_ADDR' => '198.51.100.61',
        'HTTP_USER_AGENT' => 'VisitorTest/1.0',
    ])->get('/test-page')->assertOk();

    expect(Visitor::query()->first()?->ip_address)->toBe('198.51.100.61');
});
