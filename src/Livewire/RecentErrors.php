<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Livewire;

use Illuminate\View\View;
use LaravelAnalytics\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use LaravelAnalytics\LaravelAnalytics\Support\DashboardDateRange;
use Livewire\Component;
use Livewire\WithPagination;

class RecentErrors extends Component
{
    use WithPagination;

    public string $from = '';

    public string $to = '';

    public function paginationView(): string
    {
        return 'analytics::livewire.pagination';
    }

    public function render(): View
    {
        $range = DashboardDateRange::resolveOrDefault(
            $this->from !== '' ? $this->from : null,
            $this->to !== '' ? $this->to : null,
        );

        $perPage = max(1, (int) config('analytics.dashboard.pagination.per_page', 25));

        return view('analytics::livewire.recent-errors', [
            'errors' => app(AnalyticsDashboardQuery::class)
                ->recentErrorsQuery($range)
                ->paginate($perPage),
        ]);
    }
}
