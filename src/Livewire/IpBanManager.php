<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\View\View;
use InvalidArgumentException;
use LaravelAnalytics\LaravelAnalytics\Livewire\Concerns\InteractsWithAnalyticsDashboard;
use LaravelAnalytics\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use LaravelAnalytics\LaravelAnalytics\Services\IpBanService;
use LaravelAnalytics\LaravelAnalytics\Services\IpUnbanService;
use Livewire\Component;
use Livewire\WithPagination;

class IpBanManager extends Component
{
    use InteractsWithAnalyticsDashboard;
    use WithPagination;

    public string $ipAddress = '';

    public string $reason = '';

    public ?string $expiresAt = null;

    public function paginationView(): string
    {
        return 'analytics::livewire.pagination';
    }

    public function banIp(IpBanService $banService): void
    {
        $this->authorizeDashboardAction();

        $validated = $this->validate([
            'ipAddress' => ['required', 'string', 'max:45'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'expiresAt' => ['nullable', 'date'],
        ]);

        try {
            $banService->ban(
                ip: $validated['ipAddress'],
                reason: $validated['reason'] ?? null,
                expiresAt: filled($validated['expiresAt'] ?? null) ? Carbon::parse($validated['expiresAt']) : null,
            );
        } catch (InvalidArgumentException $exception) {
            $this->addError('ipAddress', $exception->getMessage());

            return;
        }

        $this->reset(['ipAddress', 'reason', 'expiresAt']);
        $this->resetPage();
    }

    public function unbanIp(string $ip, IpUnbanService $unbanService): void
    {
        $this->authorizeDashboardAction();

        try {
            $unbanService->unban($ip);
        } catch (InvalidArgumentException $exception) {
            $this->addError('ipAddress', $exception->getMessage());
        }

        $this->resetPage();
    }

    public function render(): View
    {
        return view('analytics::livewire.ip-ban-manager', [
            'bans' => app(AnalyticsDashboardQuery::class)
                ->ipBansQuery()
                ->paginate($this->perPage()),
        ]);
    }
}
