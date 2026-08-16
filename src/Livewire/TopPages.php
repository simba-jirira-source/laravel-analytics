<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use SimbaJirira\LaravelAnalytics\Livewire\Concerns\InteractsWithAnalyticsDashboard;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use SimbaJirira\LaravelAnalytics\Support\DashboardDateRange;

class TopPages extends Component
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

        return view('analytics::livewire.top-pages', [
            'pages' => app(AnalyticsDashboardQuery::class)->topPages($range),
        ]);
    }
}
