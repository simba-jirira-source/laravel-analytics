<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Gate;
use LaravelAnalytics\LaravelAnalytics\Models\AnalyticsError;
use LaravelAnalytics\LaravelAnalytics\Models\PageView;

it('forbids guest access to the dashboard', function () {
    $this->get('/analytics')->assertForbidden();
});

it('allows authorized users to access the dashboard', function () {
    PageView::factory()->create([
        'path' => '/tracked-page',
        'viewed_at' => now()->subDay(),
    ]);

    $this->actingAs($this->dashboardUser())
        ->get('/analytics')
        ->assertOk()
        ->assertSee('Analytics Dashboard')
        ->assertSee('Overview');
});

it('forbids unauthorized authenticated users when the gate denies access', function () {
    Gate::define('viewAnalyticsDashboard', fn () => false);

    $this->actingAs($this->dashboardUser())
        ->get('/analytics')
        ->assertForbidden();
});

it('allows authorized users to view error details', function () {
    $error = AnalyticsError::factory()->create();

    $this->actingAs($this->dashboardUser())
        ->get('/analytics/errors/'.$error->getKey())
        ->assertOk()
        ->assertSee($error->message);
});
