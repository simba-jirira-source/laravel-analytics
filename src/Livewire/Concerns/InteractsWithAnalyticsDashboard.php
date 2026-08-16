<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Livewire\Concerns;

use InvalidArgumentException;
use LaravelAnalytics\LaravelAnalytics\Support\DashboardAuthorizer;
use LaravelAnalytics\LaravelAnalytics\Support\DashboardDateRange;

trait InteractsWithAnalyticsDashboard
{
    public string $from = '';

    public string $to = '';

    protected function dashboardDateRange(): DashboardDateRange
    {
        try {
            return DashboardDateRange::resolve($this->from, $this->to);
        } catch (InvalidArgumentException $exception) {
            $this->addError('from', $exception->getMessage());

            return DashboardDateRange::resolve(null, null);
        }
    }

    protected function authorizeDashboardAction(): void
    {
        app(DashboardAuthorizer::class)->authorize(auth()->user());
    }

    protected function perPage(): int
    {
        return max(1, (int) config('analytics.dashboard.pagination.per_page', 25));
    }
}
