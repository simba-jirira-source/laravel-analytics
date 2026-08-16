<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Tests;

use LaravelAnalytics\LaravelAnalytics\Http\Middleware\EnforceIpBanMiddleware;

abstract class IpBanningTestCase extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'analytics.enabled' => true,
            'analytics.ip_banning.enabled' => true,
            'analytics.ip_banning.blocked_status' => 403,
            'app.key' => 'base64:'.base64_encode(str_repeat('b', 32)),
        ]);
    }

    protected function defineRoutes($router): void
    {
        $router->middleware(['web', EnforceIpBanMiddleware::class])->group(function () use ($router): void {
            $router->get('public-page', fn () => response('ok', 200))->name('public.page');
            $router->get('analytics', fn () => response('dashboard', 200))->name('analytics.dashboard');
        });
    }

    protected function withClientIp(string $ip): static
    {
        return $this->withServerVariables(['REMOTE_ADDR' => $ip]);
    }
}
