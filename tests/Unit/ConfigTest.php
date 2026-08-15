<?php

declare(strict_types=1);

it('defaults analytics features to disabled', function () {
    expect(config('analytics.enabled'))->toBeFalse()
        ->and(config('analytics.dashboard.enabled'))->toBeFalse()
        ->and(config('analytics.tracking.traffic'))->toBeFalse()
        ->and(config('analytics.tracking.errors'))->toBeFalse()
        ->and(config('analytics.ip_banning.enabled'))->toBeFalse();
});

it('defaults privacy settings to conservative values', function () {
    expect(config('analytics.privacy.store_raw_ip'))->toBeFalse()
        ->and(config('analytics.privacy.hash_ips'))->toBeTrue()
        ->and(config('analytics.privacy.hash_salt'))->toBeNull()
        ->and(config('analytics.privacy.track_authenticated_users'))->toBeFalse()
        ->and(config('analytics.privacy.collect_referrer'))->toBeTrue()
        ->and(config('analytics.privacy.collect_user_agent'))->toBeTrue();
});

it('excludes dashboard paths from tracking by default', function () {
    expect(config('analytics.ignored.paths'))->toContain('analytics')
        ->and(config('analytics.ignored.paths'))->toContain('analytics/*')
        ->and(config('analytics.ignored.route_names'))->toContain('analytics.*');
});

it('defines retention defaults', function () {
    expect(config('analytics.retention.days'))->toBe(90)
        ->and(config('analytics.retention.prune_page_views'))->toBeTrue()
        ->and(config('analytics.retention.prune_visitors'))->toBeTrue()
        ->and(config('analytics.retention.prune_errors'))->toBeTrue();
});

it('does not bind a host user model by default', function () {
    expect(config('analytics.user.model'))->toBeNull()
        ->and(config('analytics.user.foreign_key'))->toBe('user_id');
});

it('allows host applications to override merged config', function () {
    config(['analytics.enabled' => true]);

    expect(config('analytics.enabled'))->toBeTrue();
});
