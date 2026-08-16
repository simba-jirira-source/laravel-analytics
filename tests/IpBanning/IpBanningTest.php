<?php

declare(strict_types=1);

use LaravelAnalytics\LaravelAnalytics\Models\IpBan;
use LaravelAnalytics\LaravelAnalytics\Services\IpBanService;
use LaravelAnalytics\LaravelAnalytics\Services\IpUnbanService;

it('blocks requests from banned ipv4 addresses', function () {
    IpBan::factory()->create(['ip_address' => '203.0.113.10']);

    $this->withClientIp('203.0.113.10')
        ->get('/public-page')
        ->assertForbidden();
});

it('blocks requests from banned ipv6 addresses', function () {
    IpBan::factory()->create(['ip_address' => '2001:db8::1']);

    $this->withClientIp('2001:0DB8:0000:0000:0000:0000:0000:0001')
        ->get('/public-page')
        ->assertForbidden();
});

it('does not block requests when ip banning is disabled', function () {
    config(['analytics.ip_banning.enabled' => false]);

    IpBan::factory()->create(['ip_address' => '203.0.113.10']);

    $this->withClientIp('203.0.113.10')
        ->get('/public-page')
        ->assertOk();
});

it('does not block requests when analytics is disabled', function () {
    config(['analytics.enabled' => false]);

    IpBan::factory()->create(['ip_address' => '203.0.113.10']);

    $this->withClientIp('203.0.113.10')
        ->get('/public-page')
        ->assertOk();
});

it('does not block requests for expired bans', function () {
    IpBan::factory()->expired()->create(['ip_address' => '203.0.113.10']);

    $this->withClientIp('203.0.113.10')
        ->get('/public-page')
        ->assertOk();
});

it('does not block requests after an ip is unbanned', function () {
    app(IpBanService::class)->ban('203.0.113.10');
    app(IpUnbanService::class)->unban('203.0.113.10');

    $this->withClientIp('203.0.113.10')
        ->get('/public-page')
        ->assertOk();
});

it('blocks banned ips from the analytics dashboard by default', function () {
    IpBan::factory()->create(['ip_address' => '203.0.113.10']);

    $this->withClientIp('203.0.113.10')
        ->get('/analytics')
        ->assertForbidden();
});

it('allows banned ips to reach configured bypass paths', function () {
    config(['analytics.ip_banning.bypass_paths' => ['analytics', 'analytics/*']]);

    IpBan::factory()->create(['ip_address' => '203.0.113.10']);

    $this->withClientIp('203.0.113.10')
        ->get('/analytics')
        ->assertOk();
});

it('falls back to 403 for invalid blocked status codes', function () {
    config(['analytics.ip_banning.blocked_status' => 200]);

    IpBan::factory()->create(['ip_address' => '203.0.113.10']);

    $this->withClientIp('203.0.113.10')
        ->get('/public-page')
        ->assertForbidden();
});

it('returns the configured blocked status code', function () {
    config(['analytics.ip_banning.blocked_status' => 429]);

    IpBan::factory()->create(['ip_address' => '203.0.113.10']);

    $this->withClientIp('203.0.113.10')
        ->get('/public-page')
        ->assertStatus(429);
});

it('allows requests when the client ip cannot be resolved', function () {
    IpBan::factory()->create(['ip_address' => '203.0.113.10']);

    $this->withServerVariables(['REMOTE_ADDR' => ''])
        ->get('/public-page')
        ->assertOk();
});
