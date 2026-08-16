<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Livewire\Concerns;

use InvalidArgumentException;
use SimbaJirira\LaravelAnalytics\Support\DashboardAuthorizer;
use SimbaJirira\LaravelAnalytics\Support\DashboardDateRange;

/**
 * @property string $from
 * @property string $to
 */
trait InteractsWithAnalyticsDashboard
{
    public function bootInteractsWithAnalyticsDashboard(): void
    {
        $this->authorizeDashboardAction();
    }

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
