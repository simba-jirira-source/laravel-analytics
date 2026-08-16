<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Livewire;

use Illuminate\View\View;
use LaravelAnalytics\LaravelAnalytics\Models\AnalyticsError;
use Livewire\Component;

class ErrorDetails extends Component
{
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
