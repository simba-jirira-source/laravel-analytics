<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('does not register dashboard routes when authorization is missing', function () {
    expect(Route::has('analytics.dashboard'))->toBeFalse();
});
