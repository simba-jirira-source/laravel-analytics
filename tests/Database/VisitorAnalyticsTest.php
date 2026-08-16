<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use SimbaJirira\LaravelAnalytics\Models\PageView;
use SimbaJirira\LaravelAnalytics\Models\Visitor;
use SimbaJirira\LaravelAnalytics\Services\VisitorAnalytics;
use SimbaJirira\LaravelAnalytics\Services\VisitorService;
use SimbaJirira\LaravelAnalytics\Support\AnalyticsHashSalt;
use SimbaJirira\LaravelAnalytics\Support\DefaultVisitorIdentifier;
use SimbaJirira\LaravelAnalytics\Support\IpAddressNormalizer;

beforeEach(function () {
    config([
        'app.key' => 'base64:'.base64_encode(str_repeat('c', 32)),
        'analytics.privacy.collect_user_agent' => true,
        'analytics.privacy.hash_ips' => true,
        'analytics.privacy.store_raw_ip' => false,
    ]);

    $identifier = new DefaultVisitorIdentifier(new AnalyticsHashSalt, new IpAddressNormalizer);

    $this->visitorService = new VisitorService($identifier);
    $this->visitorAnalytics = new VisitorAnalytics;
});

it('creates a unique visitor on first visit', function () {
    $request = Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '198.51.100.10',
        'HTTP_USER_AGENT' => 'VisitorTest/1.0',
    ]);

    $visitor = $this->visitorService->upsertFromRequest($request, Carbon::now());

    expect(Visitor::query()->count())->toBe(1)
        ->and($visitor->first_seen_at)->not->toBeNull()
        ->and($visitor->last_seen_at)->not->toBeNull()
        ->and($visitor->ip_address)->toBeNull();
});

it('updates repeat visitors without creating duplicates', function () {
    $request = Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '198.51.100.10',
        'HTTP_USER_AGENT' => 'VisitorTest/1.0',
    ]);

    $firstSeenAt = Carbon::parse('2026-01-01 10:00:00');
    $secondSeenAt = Carbon::parse('2026-01-02 12:00:00');

    $first = $this->visitorService->upsertFromRequest($request, $firstSeenAt);
    $second = $this->visitorService->upsertFromRequest($request, $secondSeenAt);

    expect(Visitor::query()->count())->toBe(1)
        ->and($second->id)->toBe($first->id)
        ->and($second->first_seen_at?->equalTo($firstSeenAt))->toBeTrue()
        ->and($second->last_seen_at?->equalTo($secondSeenAt))->toBeTrue();
});

it('stores raw ip only when enabled', function () {
    config(['analytics.privacy.store_raw_ip' => true]);

    $request = Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '198.51.100.20',
        'HTTP_USER_AGENT' => 'VisitorTest/1.0',
    ]);

    $visitor = $this->visitorService->upsertFromRequest($request, Carbon::now());

    expect($visitor->ip_address)->toBe('198.51.100.20');
});

it('counts unique and repeat visitors from page view activity', function () {
    $requestA = Request::create('/a', 'GET', server: [
        'REMOTE_ADDR' => '198.51.100.30',
        'HTTP_USER_AGENT' => 'VisitorTest/1.0',
    ]);

    $requestB = Request::create('/b', 'GET', server: [
        'REMOTE_ADDR' => '198.51.100.40',
        'HTTP_USER_AGENT' => 'VisitorTest/1.0',
    ]);

    $visitorA = $this->visitorService->upsertFromRequest($requestA, Carbon::now());
    $visitorB = $this->visitorService->upsertFromRequest($requestB, Carbon::now());

    PageView::factory()->create([
        'visitor_id' => $visitorA->id,
        'visitor_hash' => $visitorA->visitor_hash,
    ]);

    PageView::factory()->create([
        'visitor_id' => $visitorA->id,
        'visitor_hash' => $visitorA->visitor_hash,
    ]);

    PageView::factory()->create([
        'visitor_id' => $visitorB->id,
        'visitor_hash' => $visitorB->visitor_hash,
    ]);

    expect($this->visitorAnalytics->uniqueVisitorCount())->toBe(2)
        ->and($this->visitorAnalytics->repeatVisitorCount())->toBe(1)
        ->and($this->visitorAnalytics->isRepeatVisitor($visitorA))->toBeTrue()
        ->and($this->visitorAnalytics->isRepeatVisitor($visitorB))->toBeFalse();
});
