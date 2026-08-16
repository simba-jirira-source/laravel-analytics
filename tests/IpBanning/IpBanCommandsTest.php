<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use SimbaJirira\LaravelAnalytics\Models\IpBan;

it('bans an ip address from the cli', function () {
    $exitCode = Artisan::call('analytics:ip-ban', [
        'ip' => '203.0.113.10',
        '--reason' => 'Manual abuse block',
        '--days' => '7',
    ]);

    expect($exitCode)->toBe(0);

    $ban = IpBan::query()->first();

    expect($ban)->not->toBeNull()
        ->and($ban->ip_address)->toBe('203.0.113.10')
        ->and($ban->reason)->toBe('Manual abuse block')
        ->and($ban->expires_at)->not->toBeNull();
});

it('rejects invalid ip addresses from the cli ban command', function () {
    $this->artisan('analytics:ip-ban', [
        'ip' => 'not-an-ip',
    ])->assertFailed()
        ->expectsOutputToContain('Invalid IP address: not-an-ip');

    expect(IpBan::query()->count())->toBe(0);
});

it('unbans an ip address from the cli', function () {
    IpBan::factory()->create(['ip_address' => '203.0.113.10']);

    $this->artisan('analytics:ip-unban', [
        'ip' => '203.0.113.10',
    ])->assertSuccessful()
        ->expectsOutputToContain('Removed active ban for 203.0.113.10.');

    expect(IpBan::findActiveForIp('203.0.113.10'))->toBeNull();
});

it('reports when cli unban finds no active ban', function () {
    $this->artisan('analytics:ip-unban', [
        'ip' => '203.0.113.10',
    ])->assertSuccessful()
        ->expectsOutputToContain('No active ban found for 203.0.113.10.');
});

it('rejects invalid ip addresses from the cli unban command', function () {
    $this->artisan('analytics:ip-unban', [
        'ip' => 'bad-ip',
    ])->assertFailed()
        ->expectsOutputToContain('Invalid IP address: bad-ip');
});
