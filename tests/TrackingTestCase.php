<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Tests;

use LaravelAnalytics\LaravelAnalytics\Http\Middleware\TrackTrafficMiddleware;

abstract class TrackingTestCase extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'analytics.enabled' => true,
            'analytics.tracking.traffic' => true,
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
        ]);
    }

    protected function defineRoutes($router): void
    {
        $router->middleware(['web', TrackTrafficMiddleware::class])->group(function () use ($router): void {
            $router->get('test-page', fn () => response('ok', 200))->name('test.page');
            $router->post('test-form', fn () => response('created', 201))->name('test.form');
            $router->get('analytics', fn () => response('dashboard', 200))->name('analytics.dashboard');
            $router->match(['OPTIONS'], 'test-options', fn () => response('', 204))->name('test.options');
            $router->get('ignored-route', fn () => response('ignored', 200))->name('ignored.route');
            $router->get('status-test', fn () => response('', 404))->name('status.test');
        });
    }
}
