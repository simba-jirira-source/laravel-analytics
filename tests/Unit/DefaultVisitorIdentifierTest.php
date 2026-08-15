<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use LaravelAnalytics\LaravelAnalytics\Support\DefaultVisitorIdentifier;

beforeEach(function () {
    config([
        'app.key' => 'base64:'.base64_encode(str_repeat('b', 32)),
        'analytics.privacy.collect_user_agent' => true,
        'analytics.privacy.hash_ips' => true,
    ]);

    $this->identifier = new DefaultVisitorIdentifier;
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

it('hashes ip addresses without storing the raw value in the identifier', function () {
    $hash = $this->identifier->hashIp('203.0.113.10');

    expect($hash)->toHaveLength(64)
        ->and($hash)->not->toBe('203.0.113.10');
});

it('omits ip hash when hashing is disabled', function () {
    config(['analytics.privacy.hash_ips' => false]);

    expect($this->identifier->hashIp('203.0.113.10'))->toBeNull();
});
