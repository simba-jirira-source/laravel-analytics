<?php

declare(strict_types=1);

use LaravelAnalytics\LaravelAnalytics\Support\ErrorFingerprintGenerator;

beforeEach(function () {
    $this->generator = new ErrorFingerprintGenerator;
});

it('generates stable fingerprints for identical exceptions', function () {
    $exception = runtimeExceptionWithMessage('Same message');

    expect($this->generator->generate($exception))->toBe($this->generator->generate($exception));
});

it('generates different fingerprints for different exception classes', function () {
    expect($this->generator->generate(new RuntimeException('Same message')))
        ->not->toBe($this->generator->generate(new InvalidArgumentException('Same message')));
});

it('redacts sensitive patterns from fingerprint input', function () {
    $first = runtimeExceptionWithMessage('Authentication failed for password=secret123');
    $second = runtimeExceptionWithMessage('Authentication failed for password=other456');

    expect($this->generator->generate($first))->toBe($this->generator->generate($second));
});
