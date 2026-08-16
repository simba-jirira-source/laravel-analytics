<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Livewire;

use Illuminate\View\View;
use LaravelAnalytics\LaravelAnalytics\Livewire\Concerns\InteractsWithAnalyticsDashboard;
use Livewire\Attributes\Url;
use Livewire\Component;

class AnalyticsDashboard extends Component
{
    use InteractsWithAnalyticsDashboard;

    #[Url(as: 'from', history: true)]
    public string $from = '';

    #[Url(as: 'to', history: true)]
    public string $to = '';

    public function mount(): void
    {
        if ($this->from === '' && $this->to === '') {
            $range = $this->dashboardDateRange();
            $this->from = $range->from->toDateString();
            $this->to = $range->to->toDateString();
        }
    }

    public function applyFilters(): void
    {
        $this->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $this->dashboardDateRange();
    }

    public function render(): View
    {
        return view('analytics::livewire.analytics-dashboard')
            ->layout('analytics::layouts.dashboard')
            ->title('Analytics Dashboard');
    }
}
