<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use LaravelAnalytics\LaravelAnalytics\Livewire\AnalyticsDashboard;
use LaravelAnalytics\LaravelAnalytics\Livewire\ErrorDetails;

if (! config('analytics.dashboard.enabled') || blank(config('analytics.dashboard.authorization'))) {
    return;
}

/** @var list<string> $middleware */
$middleware = array_values(array_unique([
    ...config('analytics.dashboard.middleware', ['web']),
    'analytics.dashboard',
]));

$path = trim((string) config('analytics.dashboard.path', 'analytics'), '/');
$routePrefix = (string) config('analytics.dashboard.route_prefix', 'analytics.');

Route::middleware($middleware)
    ->prefix($path)
    ->name($routePrefix)
    ->group(function (): void {
        Route::get('/', AnalyticsDashboard::class)->name('dashboard');
        Route::get('/errors/{error}', ErrorDetails::class)->name('errors.show');
    });
