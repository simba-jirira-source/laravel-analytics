<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use LaravelAnalytics\LaravelAnalytics\Models\IpBan;
use LaravelAnalytics\LaravelAnalytics\Services\IpBanService;
use LaravelAnalytics\LaravelAnalytics\Services\IpUnbanService;

beforeEach(function () {
    config([
        'analytics.enabled' => true,
        'analytics.ip_banning.enabled' => true,
    ]);

    $this->banService = app(IpBanService::class);
    $this->unbanService = app(IpUnbanService::class);
});

it('bans a valid ipv4 address', function () {
    $ban = $this->banService->ban('203.0.113.10', reason: 'Abuse');

    expect($ban->ip_address)->toBe('203.0.113.10')
        ->and($ban->reason)->toBe('Abuse')
        ->and($ban->isCurrentlyActive())->toBeTrue();
});

it('stores normalized ipv6 addresses', function () {
    $ban = $this->banService->ban('2001:0DB8:0000:0000:0000:0000:0000:0001');

    expect($ban->ip_address)->toBe('2001:db8::1')
        ->and(IpBan::findActiveForIp('2001:db8::1'))->not->toBeNull();
});

it('reactivates an existing ban for the same ip', function () {
    $first = $this->banService->ban('203.0.113.20', reason: 'First reason');
    $this->unbanService->unban('203.0.113.20');

    $second = $this->banService->ban('203.0.113.20', reason: 'Second reason');

    expect(IpBan::query()->count())->toBe(1)
        ->and($second->id)->toBe($first->id)
        ->and($second->reason)->toBe('Second reason')
        ->and($second->isCurrentlyActive())->toBeTrue();
});

it('persists optional expiry timestamps', function () {
    $expiresAt = Carbon::parse('2030-01-01 00:00:00');

    $ban = $this->banService->ban('203.0.113.30', expiresAt: $expiresAt);

    expect($ban->expires_at?->equalTo($expiresAt))->toBeTrue();
});

it('rejects invalid ip addresses when banning', function () {
    expect(fn () => $this->banService->ban('invalid-ip'))
        ->toThrow(InvalidArgumentException::class);
});

it('rejects cidr ranges when banning', function () {
    expect(fn () => $this->banService->ban('10.0.0.0/8'))
        ->toThrow(InvalidArgumentException::class, 'CIDR ranges are not supported.');
});

it('unbans active bans for an ip', function () {
    $this->banService->ban('203.0.113.40');

    expect($this->unbanService->unban('203.0.113.40'))->toBeTrue()
        ->and(IpBan::findActiveForIp('203.0.113.40'))->toBeNull();
});

it('does not treat expired bans as active', function () {
    IpBan::factory()->expired()->create([
        'ip_address' => '203.0.113.50',
    ]);

    expect(IpBan::findActiveForIp('203.0.113.50'))->toBeNull();
});

it('does not treat inactive bans as active', function () {
    IpBan::factory()->inactive()->create([
        'ip_address' => '203.0.113.60',
    ]);

    expect(IpBan::findActiveForIp('203.0.113.60'))->toBeNull();
});
