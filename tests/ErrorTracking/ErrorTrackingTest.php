<?php

declare(strict_types=1);

use LaravelAnalytics\LaravelAnalytics\Contracts\ErrorRecorder;
use LaravelAnalytics\LaravelAnalytics\Http\Middleware\RecordErrorsMiddleware;
use LaravelAnalytics\LaravelAnalytics\Models\AnalyticsError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

it('records supported http exceptions and preserves laravel exception behaviour', function () {
    $this->withoutExceptionHandling();

    expect(fn () => $this->get('/throws-runtime'))
        ->toThrow(RuntimeException::class, 'Runtime failure for analytics test.');

    $error = AnalyticsError::query()->first();

    expect($error)->not->toBeNull()
        ->and($error->exception_class)->toBe(RuntimeException::class)
        ->and($error->path)->toBe('/throws-runtime')
        ->and($error->route_name)->toBe('errors.runtime')
        ->and($error->method)->toBe('GET');
});

it('does not record errors when error tracking is disabled', function () {
    config(['analytics.tracking.errors' => false]);

    $this->withoutExceptionHandling();

    expect(fn () => $this->get('/throws-runtime'))
        ->toThrow(RuntimeException::class);

    expect(AnalyticsError::query()->count())->toBe(0);
});

it('groups matching errors and increments occurrence count', function () {
    $this->withoutExceptionHandling();

    expect(fn () => $this->get('/throws-repeated'))->toThrow(RuntimeException::class);
    expect(fn () => $this->get('/throws-repeated'))->toThrow(RuntimeException::class);

    expect(AnalyticsError::query()->count())->toBe(1)
        ->and(AnalyticsError::query()->first()?->occurrence_count)->toBe(2);
});

it('stores http exception status codes', function () {
    $this->withoutExceptionHandling();

    expect(fn () => $this->get('/throws-http'))->toThrow(NotFoundHttpException::class);

    expect(AnalyticsError::query()->first()?->status_code)->toBe(404);
});

it('does not persist sensitive request payload data', function () {
    $this->withoutExceptionHandling();

    expect(fn () => $this->post('/throws-with-body', [
        'password' => 'super-secret-password',
        'email' => 'user@example.com',
    ]))->toThrow(RuntimeException::class);

    $serialized = json_encode(AnalyticsError::query()->first()?->getAttributes(), JSON_THROW_ON_ERROR);

    expect($serialized)->not->toContain('super-secret-password')
        ->and($serialized)->not->toContain('user@example.com');
});

it('does not self-track analytics dashboard routes by default', function () {
    $router = $this->app->make('router');
    $router->middleware(['web', RecordErrorsMiddleware::class])
        ->get('analytics/throws', function (): never {
            throw new RuntimeException('Dashboard failure.');
        })->name('analytics.throws');

    $this->withoutExceptionHandling();

    expect(fn () => $this->get('/analytics/throws'))->toThrow(RuntimeException::class);

    expect(AnalyticsError::query()->count())->toBe(0);
});

it('rethrows the original exception when analytics recording fails', function () {
    $this->mock(ErrorRecorder::class, function ($mock): void {
        $mock->shouldReceive('record')
            ->once()
            ->andThrow(new RuntimeException('Analytics recorder failure'));
    });

    $this->withoutExceptionHandling();

    expect(fn () => $this->get('/throws-runtime'))
        ->toThrow(RuntimeException::class, 'Runtime failure for analytics test.');
});
