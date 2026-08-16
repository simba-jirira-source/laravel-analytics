<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use SimbaJirira\LaravelAnalytics\Contracts\ErrorRecorder;
use SimbaJirira\LaravelAnalytics\Http\Middleware\RecordErrorsMiddleware;
use SimbaJirira\LaravelAnalytics\Models\AnalyticsError;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsErrorRecorder;
use SimbaJirira\LaravelAnalytics\Support\ErrorFingerprintGenerator;
use SimbaJirira\LaravelAnalytics\Support\RequestExclusionChecker;
use SimbaJirira\LaravelAnalytics\Support\SafeExceptionMetadataExtractor;
use SimbaJirira\LaravelAnalytics\Support\SensitiveMessageRedactor;

beforeEach(function () {
    config([
        'analytics.enabled' => true,
        'analytics.tracking.errors' => true,
    ]);

    $redactor = new SensitiveMessageRedactor;

    $this->recorder = new AnalyticsErrorRecorder(
        new RequestExclusionChecker,
        new ErrorFingerprintGenerator($redactor),
        new SafeExceptionMetadataExtractor($redactor),
    );
});

it('records a new analytics error', function () {
    $request = Request::create('/broken', 'GET', server: ['REQUEST_URI' => '/broken']);

    $this->recorder->record(new RuntimeException('Initial failure'), $request);

    $error = AnalyticsError::query()->first();

    expect($error)->not->toBeNull()
        ->and($error->exception_class)->toBe(RuntimeException::class)
        ->and($error->occurrence_count)->toBe(1);
});

it('aggregates repeated errors by fingerprint', function () {
    $request = Request::create('/broken', 'GET');

    $this->recorder->record(runtimeExceptionWithMessage('Repeated failure'), $request);
    $this->recorder->record(runtimeExceptionWithMessage('Repeated failure'), $request);

    expect(AnalyticsError::query()->count())->toBe(1)
        ->and(AnalyticsError::query()->first()?->occurrence_count)->toBe(2);
});

it('does not record errors when error tracking is disabled', function () {
    config(['analytics.tracking.errors' => false]);

    $this->recorder->record(new RuntimeException('Ignored failure'), Request::create('/broken', 'GET'));

    expect(AnalyticsError::query()->count())->toBe(0);
});

it('does not record package recorder failures', function () {
    $request = Request::create('/broken', 'GET');
    $exception = new RuntimeException('Recorder exploded');
    $reflection = new ReflectionClass($exception);
    $file = $reflection->getProperty('file');
    $file->setAccessible(true);
    $file->setValue($exception, str_replace('\\', '/', __DIR__.'/../../src/Services/AnalyticsErrorRecorder.php'));

    $this->recorder->record($exception, $request);

    expect(AnalyticsError::query()->count())->toBe(0);
});

it('isolates recorder failures without throwing', function () {
    $mock = Mockery::mock(ErrorRecorder::class);
    $mock->shouldReceive('record')->once()->andThrow(new RuntimeException('Recorder exploded'));

    $middleware = new RecordErrorsMiddleware(
        new RequestExclusionChecker,
        $mock,
    );

    $request = Request::create('/broken', 'GET');
    $exception = new RuntimeException('Original application failure');

    expect(fn () => $middleware->handle($request, function () use ($exception): never {
        throw $exception;
    }))->toThrow(RuntimeException::class, 'Original application failure');
});
