<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('does not register dashboard routes when the dashboard is disabled', function () {
    expect(Route::has('analytics.dashboard'))->toBeFalse();
});
