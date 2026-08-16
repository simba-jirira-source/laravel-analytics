<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use LaravelAnalytics\LaravelAnalytics\Support\RequestExclusionChecker;

beforeEach(function () {
    config([
        'analytics.enabled' => true,
        'analytics.tracking.traffic' => true,
    ]);

    $this->checker = new RequestExclusionChecker;
});

it('detects when tracking is disabled', function () {
    config(['analytics.enabled' => false]);

    expect($this->checker->isTrackingEnabled())->toBeFalse();
});

it('matches ignored paths using wildcards', function () {
    expect($this->checker->isIgnoredPath('analytics'))->toBeTrue()
        ->and($this->checker->isIgnoredPath('analytics/dashboard'))->toBeTrue()
        ->and($this->checker->isIgnoredPath('test-page'))->toBeFalse();
});

it('matches ignored route name patterns', function () {
    expect($this->checker->isIgnoredRouteName('analytics.dashboard'))->toBeTrue()
        ->and($this->checker->isIgnoredRouteName('test.page'))->toBeFalse();
});

it('matches ignored http methods', function () {
    expect($this->checker->isIgnoredMethod('OPTIONS'))->toBeTrue()
        ->and($this->checker->isIgnoredMethod('GET'))->toBeFalse();
});

it('respects excluded status codes', function () {
    config(['analytics.excluded_status_codes' => [404, 500]]);

    expect($this->checker->shouldRecordStatus(200))->toBeTrue()
        ->and($this->checker->shouldRecordStatus(404))->toBeFalse();
});

it('evaluates full request exclusion', function () {
    $request = Request::create('/analytics', 'GET');
    $request->setRouteResolver(fn () => new class
    {
        public function getName(): string
        {
            return 'analytics.dashboard';
        }
    });

    expect($this->checker->shouldTrackRequest($request))->toBeFalse();
});

it('does not bypass ip bans using ignored tracking paths by default', function () {
    $request = Request::create('/analytics', 'GET');

    expect($this->checker->shouldBypassIpBan($request))->toBeFalse();
});

it('bypasses ip bans for configured paths', function () {
    config(['analytics.ip_banning.bypass_paths' => ['analytics', 'analytics/*']]);

    $request = Request::create('/analytics/dashboard', 'GET');

    expect($this->checker->shouldBypassIpBan($request))->toBeTrue();
});
