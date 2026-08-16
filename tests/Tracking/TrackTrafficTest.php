<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use SimbaJirira\LaravelAnalytics\Http\Middleware\TrackTrafficMiddleware;
use SimbaJirira\LaravelAnalytics\Models\PageView;

it('records a page view for enabled traffic tracking', function () {
    $this->get('/test-page')
        ->assertOk();

    $pageView = PageView::query()->first();

    expect($pageView)->not->toBeNull()
        ->and($pageView->path)->toBe('/test-page')
        ->and($pageView->method)->toBe('GET')
        ->and($pageView->route_name)->toBe('test.page')
        ->and($pageView->status_code)->toBe(200)
        ->and($pageView->visitor_hash)->not->toBeEmpty();
});

it('does not record page views when analytics is disabled', function () {
    config([
        'analytics.enabled' => false,
        'analytics.tracking.traffic' => false,
    ]);

    $this->get('/test-page')->assertOk();

    expect(PageView::query()->count())->toBe(0);
});

it('does not record page views when traffic tracking is disabled', function () {
    config(['analytics.tracking.traffic' => false]);

    $this->get('/test-page')->assertOk();

    expect(PageView::query()->count())->toBe(0);
});

it('does not record ignored paths', function () {
    config([
        'analytics.ignored.paths' => array_merge(
            config('analytics.ignored.paths'),
            ['test-page'],
        ),
    ]);

    $this->get('/test-page')->assertOk();

    expect(PageView::query()->count())->toBe(0);
});

it('does not record ignored route names', function () {
    config([
        'analytics.ignored.route_names' => array_merge(
            config('analytics.ignored.route_names'),
            ['ignored.route'],
        ),
    ]);

    $this->get('/ignored-route')->assertOk();

    expect(PageView::query()->count())->toBe(0);
});

it('does not record ignored http methods', function () {
    $this->call('OPTIONS', '/test-options')->assertNoContent();

    expect(PageView::query()->count())->toBe(0);
});

it('does not self-track analytics dashboard routes by default', function () {
    $this->get('/analytics')->assertOk();

    expect(PageView::query()->count())->toBe(0);
});

it('captures the response status code', function () {
    $this->get('/status-test')->assertNotFound();

    expect(PageView::query()->first()?->status_code)->toBe(404);
});

it('records a sane request duration', function () {
    Route::middleware(['web', TrackTrafficMiddleware::class])->get('slow-page', function () {
        usleep(5000);

        return response('slow', 200);
    })->name('slow.page');

    $this->get('/slow-page')->assertOk();

    expect(PageView::query()->first()?->duration_ms)->toBeGreaterThanOrEqual(0)
        ->and(PageView::query()->first()?->duration_ms)->toBeLessThan(5000);
});

it('collects referrer metadata when enabled', function () {
    $this->withHeaders(['Referer' => 'https://example.com/previous-page?token=secret'])
        ->get('/test-page')
        ->assertOk();

    $pageView = PageView::query()->first();

    expect($pageView?->referrer_host)->toBe('example.com')
        ->and($pageView?->referrer_url)->toBe('https://example.com/previous-page');
});

it('strips query strings from stored referrer urls', function () {
    $this->withHeaders(['Referer' => 'https://example.com/path?session=abc123'])
        ->get('/test-page')
        ->assertOk();

    expect(PageView::query()->first()?->referrer_url)->toBe('https://example.com/path');
});

it('does not persist request body or sensitive payload fields', function () {
    $this->post('/test-form', [
        'password' => 'secret-password',
        'email' => 'user@example.com',
    ])->assertCreated();

    $pageView = PageView::query()->first();

    expect($pageView)->not->toBeNull()
        ->and($pageView->path)->toBe('/test-form')
        ->and($pageView->method)->toBe('POST');

    $serialized = json_encode($pageView->getAttributes(), JSON_THROW_ON_ERROR);

    expect($serialized)->not->toContain('secret-password')
        ->and($serialized)->not->toContain('user@example.com');
});

it('does not record excluded response status codes', function () {
    config(['analytics.excluded_status_codes' => [404]]);

    $this->get('/status-test')->assertNotFound();

    expect(PageView::query()->count())->toBe(0);
});
