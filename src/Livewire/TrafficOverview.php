<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use SimbaJirira\LaravelAnalytics\Livewire\Concerns\InteractsWithAnalyticsDashboard;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use SimbaJirira\LaravelAnalytics\Support\DashboardDateRange;

class TrafficOverview extends Component
{
    use InteractsWithAnalyticsDashboard;

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
