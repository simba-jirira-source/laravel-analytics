<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Gate;
use LaravelAnalytics\LaravelAnalytics\Livewire\AnalyticsDashboard;
use LaravelAnalytics\LaravelAnalytics\Livewire\ErrorDetails;
use LaravelAnalytics\LaravelAnalytics\Livewire\IpBanManager;
use LaravelAnalytics\LaravelAnalytics\Livewire\RecentErrors;
use LaravelAnalytics\LaravelAnalytics\Livewire\TrafficOverview;
use LaravelAnalytics\LaravelAnalytics\Models\AnalyticsError;
use LaravelAnalytics\LaravelAnalytics\Models\IpBan;
use LaravelAnalytics\LaravelAnalytics\Models\PageView;
use LaravelAnalytics\LaravelAnalytics\Tests\Support\DashboardUser;
use Livewire\Livewire;

it('renders dashboard overview metrics', function () {
    PageView::factory()->create([
        'path' => '/metrics-page',
        'viewed_at' => now()->subDay(),
    ]);
    AnalyticsError::factory()->create(['last_occurred_at' => now()->subDay()]);

    Livewire::actingAs($this->dashboardUser())
        ->test(TrafficOverview::class, [
            'from' => now()->subDays(7)->toDateString(),
            'to' => now()->toDateString(),
        ])
        ->assertSee('Page Views')
        ->assertSee('1');
});

it('renders the dashboard shell and nested components', function () {
    Livewire::actingAs($this->dashboardUser())
        ->test(AnalyticsDashboard::class)
        ->assertSee('Analytics Dashboard')
        ->assertSee('Top Pages')
        ->assertSee('Recent Errors');
});

it('validates dashboard date filters', function () {
    Livewire::actingAs($this->dashboardUser())
        ->test(AnalyticsDashboard::class)
        ->set('from', now()->toDateString())
        ->set('to', now()->subDays(7)->toDateString())
        ->call('applyFilters')
        ->assertHasErrors(['to']);
});

it('bans and unbans ip addresses from the dashboard', function () {
    Livewire::actingAs($this->dashboardUser())
        ->test(IpBanManager::class)
        ->set('ipAddress', '203.0.113.10')
        ->set('reason', 'Abusive traffic')
        ->call('banIp')
        ->assertHasNoErrors();

    expect(IpBan::findActiveForIp('203.0.113.10'))->not->toBeNull();

    Livewire::actingAs($this->dashboardUser())
        ->test(IpBanManager::class)
        ->call('unbanIp', '203.0.113.10')
        ->assertHasNoErrors();

    expect(IpBan::findActiveForIp('203.0.113.10'))->toBeNull();
});

it('rejects invalid ip addresses in the ban manager', function () {
    Livewire::actingAs($this->dashboardUser())
        ->test(IpBanManager::class)
        ->set('ipAddress', 'not-an-ip')
        ->call('banIp')
        ->assertHasErrors(['ipAddress']);
});

it('forbids guest users from dashboard components', function () {
    Livewire::test(TrafficOverview::class)
        ->assertForbidden();

    Livewire::test(RecentErrors::class)
        ->assertForbidden();
});

it('forbids unauthorized authenticated users from dashboard components', function () {
    Gate::define('viewAnalyticsDashboard', fn (): bool => false);

    Livewire::actingAs($this->dashboardUser())
        ->test(TrafficOverview::class)
        ->assertForbidden();
})->after(function (): void {
    Gate::define('viewAnalyticsDashboard', fn (?DashboardUser $user = null): bool => $user !== null);
});

it('forbids guest users from viewing error details', function () {
    $error = AnalyticsError::factory()->create();

    Livewire::test(ErrorDetails::class, ['error' => $error])
        ->assertForbidden();
});

it('forbids guest users from mutating ip bans', function () {
    Livewire::test(IpBanManager::class)
        ->assertForbidden();
});
