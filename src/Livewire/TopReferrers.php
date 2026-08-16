<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Livewire;

use Illuminate\View\View;
use LaravelAnalytics\LaravelAnalytics\Livewire\Concerns\InteractsWithAnalyticsDashboard;
use LaravelAnalytics\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use LaravelAnalytics\LaravelAnalytics\Support\DashboardDateRange;
use Livewire\Component;

class TopReferrers extends Component
{
    use InteractsWithAnalyticsDashboard;

    public string $from = '';

    public string $to = '';

    public function render(): View
    {
        $range = DashboardDateRange::resolveOrDefault(
            $this->from !== '' ? $this->from : null,
            $this->to !== '' ? $this->to : null,
        );

        return view('analytics::livewire.top-referrers', [
            'referrers' => app(AnalyticsDashboardQuery::class)->topReferrers($range),
        ]);
    }
}
