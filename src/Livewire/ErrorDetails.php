<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use SimbaJirira\LaravelAnalytics\Livewire\Concerns\InteractsWithAnalyticsDashboard;
use SimbaJirira\LaravelAnalytics\Models\AnalyticsError;

class ErrorDetails extends Component
{
    use InteractsWithAnalyticsDashboard;

    public AnalyticsError $error;

    public function mount(AnalyticsError $error): void
    {
        $this->error = $error;
    }

    public function render(): View
    {
        return view('analytics::livewire.error-details', [
            'error' => $this->error,
        ])->layout('analytics::layouts.dashboard')
            ->title('Error Details');
    }
}
