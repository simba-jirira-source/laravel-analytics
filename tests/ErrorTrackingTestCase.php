<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Tests;

use RuntimeException;
use SimbaJirira\LaravelAnalytics\Http\Middleware\RecordErrorsMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class ErrorTrackingTestCase extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'analytics.enabled' => true,
            'analytics.tracking.traffic' => false,
            'analytics.tracking.errors' => true,
            'app.key' => 'base64:'.base64_encode(str_repeat('d', 32)),
        ]);
    }

    protected function defineRoutes($router): void
    {
        $router->middleware(['web', RecordErrorsMiddleware::class])->group(function () use ($router): void {
            $router->get('throws-runtime', function (): never {
                throw new RuntimeException('Runtime failure for analytics test.');
            })->name('errors.runtime');

            $router->post('throws-with-body', function (): never {
                throw new RuntimeException('Generic failure.');
            })->name('errors.with-body');

            $router->get('throws-http', function (): never {
                throw new NotFoundHttpException('Missing resource.');
            })->name('errors.http');

            $router->get('throws-repeated', function (): never {
                throw new RuntimeException('Repeated runtime failure.');
            })->name('errors.repeated');

            $router->get('analytics', fn () => response('dashboard', 200))->name('analytics.dashboard');
        });
    }
}
