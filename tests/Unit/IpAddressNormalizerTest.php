<?php

declare(strict_types=1);

use LaravelAnalytics\LaravelAnalytics\Support\IpAddressNormalizer;

beforeEach(function () {
    $this->normalizer = new IpAddressNormalizer;
});

it('normalizes ipv4 addresses', function () {
    expect($this->normalizer->normalize('203.0.113.10'))->toBe('203.0.113.10');
});

it('normalizes ipv6 addresses', function () {
    $normalized = $this->normalizer->normalize('2001:0DB8:0000:0000:0000:0000:0000:0001');

    expect($normalized)->toBe('2001:db8::1');
});

it('normalizes ipv4 mapped ipv6 addresses', function () {
    expect($this->normalizer->normalize('::ffff:203.0.113.10'))->toBe('203.0.113.10');
});

it('returns an empty string for missing ip values', function () {
    expect($this->normalizer->normalize(null))->toBe('');
});
