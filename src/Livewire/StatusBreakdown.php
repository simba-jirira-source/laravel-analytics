<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use SimbaJirira\LaravelAnalytics\Livewire\Concerns\InteractsWithAnalyticsDashboard;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use SimbaJirira\LaravelAnalytics\Support\DashboardDateRange;

class StatusBreakdown extends Component
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

        return view('analytics::livewire.status-breakdown', [
            'statuses' => app(AnalyticsDashboardQuery::class)->statusBreakdown($range),
        ]);
    }
}
