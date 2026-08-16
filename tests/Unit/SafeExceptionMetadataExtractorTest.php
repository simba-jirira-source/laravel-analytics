<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use LaravelAnalytics\LaravelAnalytics\Support\SafeExceptionMetadataExtractor;
use LaravelAnalytics\LaravelAnalytics\Support\SensitiveMessageRedactor;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function () {
    $this->extractor = new SafeExceptionMetadataExtractor(new SensitiveMessageRedactor);
});

it('extracts safe exception metadata from requests', function () {
    $request = Request::create('/safe-path', 'POST');
    $request->setRouteResolver(fn (): object => new class
    {
        public function getName(): string
        {
            return 'safe.route';
        }
    });

    $metadata = $this->extractor->extract(
        new RuntimeException('Safe summary message'),
        $request,
    );

    expect($metadata['exception_class'])->toBe(RuntimeException::class)
        ->and($metadata['message'])->toBe('Safe summary message')
        ->and($metadata['route_name'])->toBe('safe.route')
        ->and($metadata['path'])->toBe('/safe-path')
        ->and($metadata['method'])->toBe('POST')
        ->and($metadata['status_code'])->toBe(500);
});

it('captures http exception status codes', function () {
    $metadata = $this->extractor->extract(
        new NotFoundHttpException('Missing resource.'),
        Request::create('/missing', 'GET'),
    );

    expect($metadata['status_code'])->toBe(404);
});

it('redacts sensitive patterns from stored messages', function () {
    $metadata = $this->extractor->extract(
        new RuntimeException('Invalid token=super-secret-value provided'),
        Request::create('/auth', 'GET'),
    );

    expect($metadata['message'])->toBe('Invalid token=[redacted] provided')
        ->and($metadata['message'])->not->toContain('super-secret-value');
});
