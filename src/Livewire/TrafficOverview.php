<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Livewire;

use Illuminate\View\View;
use LaravelAnalytics\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use LaravelAnalytics\LaravelAnalytics\Support\DashboardDateRange;
use Livewire\Component;

class TrafficOverview extends Component
{
    public string $from = '';

    public string $to = '';

    /**
     * @return array<string, int>
     */
    public function metrics(): array
    {
        $range = DashboardDateRange::resolveOrDefault(
            $this->from !== '' ? $this->from : null,
            $this->to !== '' ? $this->to : null,
        );

        return app(AnalyticsDashboardQuery::class)->overviewMetrics($range);
    }

    public function render(): View
    {
        return view('analytics::livewire.traffic-overview', [
            'metrics' => $this->metrics(),
        ]);
    }
}
