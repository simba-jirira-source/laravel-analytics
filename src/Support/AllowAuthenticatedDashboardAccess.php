<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Support;

use Illuminate\Contracts\Auth\Authenticatable;

class AllowAuthenticatedDashboardAccess
{
    public function __invoke(?Authenticatable $user): bool
    {
        return $user !== null;
    }
}
