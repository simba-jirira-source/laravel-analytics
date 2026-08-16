<?php

declare(strict_types=1);

use LaravelAnalytics\LaravelAnalytics\Support\IpAddressValidator;

beforeEach(function () {
    $this->validator = app(IpAddressValidator::class);
});

it('accepts valid ipv4 addresses', function () {
    expect($this->validator->validate('203.0.113.10'))->toBe('203.0.113.10');
});

it('accepts and normalizes valid ipv6 addresses', function () {
    expect($this->validator->validate('2001:0DB8:0000:0000:0000:0000:0000:0001'))
        ->toBe('2001:db8::1');
});

it('accepts ipv4 mapped ipv6 addresses as ipv4', function () {
    expect($this->validator->validate('::ffff:203.0.113.10'))->toBe('203.0.113.10');
});

it('rejects invalid ip addresses', function () {
    expect(fn () => $this->validator->validate('not-an-ip'))
        ->toThrow(InvalidArgumentException::class, 'Invalid IP address: not-an-ip');
});

it('rejects empty ip addresses', function () {
    expect(fn () => $this->validator->validate('   '))
        ->toThrow(InvalidArgumentException::class, 'An IP address is required.');
});

it('rejects cidr ranges', function () {
    expect(fn () => $this->validator->validate('203.0.113.0/24'))
        ->toThrow(InvalidArgumentException::class, 'CIDR ranges are not supported.');
});

it('reports invalid addresses through is valid', function () {
    expect($this->validator->isValid('203.0.113.10'))->toBeTrue()
        ->and($this->validator->isValid('203.0.113.0/24'))->toBeFalse();
});
