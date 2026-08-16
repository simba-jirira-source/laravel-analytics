<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use LaravelAnalytics\LaravelAnalytics\Support\AnalyticsHashSalt;
use LaravelAnalytics\LaravelAnalytics\Support\DefaultVisitorIdentifier;
use LaravelAnalytics\LaravelAnalytics\Support\IpAddressNormalizer;

beforeEach(function () {
    config([
        'app.key' => 'base64:'.base64_encode(str_repeat('b', 32)),
        'analytics.privacy.collect_user_agent' => true,
        'analytics.privacy.hash_ips' => true,
        'analytics.privacy.track_authenticated_users' => false,
    ]);

    $this->identifier = new DefaultVisitorIdentifier(
        new AnalyticsHashSalt,
        new IpAddressNormalizer,
    );
});

it('produces a stable hashed visitor identifier', function () {
    $request = Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]);

    $first = $this->identifier->identify($request);
    $second = $this->identifier->identify($request);

    expect($first)->toBe($second)
        ->and($first)->toHaveLength(64)
        ->and($first)->not->toContain('127.0.0.1')
        ->and($first)->not->toContain('TestAgent');
});

it('produces different identifiers for different ipv4 addresses', function () {
    $first = $this->identifier->identify(Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '203.0.113.1',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]));

    $second = $this->identifier->identify(Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '203.0.113.2',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]));

    expect($first)->not->toBe($second);
});

it('treats ipv4 mapped ipv6 addresses as their ipv4 equivalent', function () {
    $ipv4 = $this->identifier->identify(Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '203.0.113.10',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]));

    $mapped = $this->identifier->identify(Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '::ffff:203.0.113.10',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]));

    expect($ipv4)->toBe($mapped);
});

it('produces different identifiers for different ipv6 addresses', function () {
    $first = $this->identifier->identify(Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '2001:db8::1',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]));

    $second = $this->identifier->identify(Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '2001:db8::2',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]));

    expect($first)->not->toBe($second);
});

it('changes the identifier when the user agent is disabled in config', function () {
    $withAgent = $this->identifier->identify(Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]));

    config(['analytics.privacy.collect_user_agent' => false]);

    $withoutAgent = $this->identifier->identify(Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]));

    expect($withAgent)->not->toBe($withoutAgent);
});

it('hashes ip addresses without storing the raw value in the identifier', function () {
    $hash = $this->identifier->hashIp('203.0.113.10');

    expect($hash)->toHaveLength(64)
        ->and($hash)->not->toBe('203.0.113.10');
});

it('omits ip hash when hashing is disabled', function () {
    config(['analytics.privacy.hash_ips' => false]);

    expect($this->identifier->hashIp('203.0.113.10'))->toBeNull();
});

it('uses a configured hash salt instead of the app key', function () {
    config(['analytics.privacy.hash_salt' => 'custom-salt-value']);

    $hash = $this->identifier->identify(Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]));

    config(['analytics.privacy.hash_salt' => 'different-salt']);

    $otherHash = $this->identifier->identify(Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]));

    expect($hash)->not->toBe($otherHash);
});

it('includes authenticated users in the identifier when enabled', function () {
    config(['analytics.privacy.track_authenticated_users' => true]);

    $user = new class
    {
        public function getAuthIdentifier(): int
        {
            return 42;
        }
    };

    $authenticated = Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]);
    $authenticated->setUserResolver(fn (): object => $user);

    $guest = Request::create('/test', 'GET', server: [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_USER_AGENT' => 'TestAgent/1.0',
    ]);

    expect($this->identifier->identify($authenticated))
        ->not->toBe($this->identifier->identify($guest));
});
