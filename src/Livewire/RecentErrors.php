<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use SimbaJirira\LaravelAnalytics\Livewire\Concerns\InteractsWithAnalyticsDashboard;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use SimbaJirira\LaravelAnalytics\Support\DashboardDateRange;

class RecentErrors extends Component
{
    use InteractsWithAnalyticsDashboard;
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

        $perPage = $this->perPage();

        return view('analytics::livewire.recent-errors', [
            'errors' => app(AnalyticsDashboardQuery::class)
                ->recentErrorsQuery($range)
                ->paginate($perPage),
        ]);
    }
}
