<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;

class DashboardAuthorizer
{
    public function authorize(?Authenticatable $user = null): void
    {
        if (! $this->allowed($user)) {
            abort(403);
        }
    }

    public function allowed(?Authenticatable $user = null): bool
    {
        if (! (bool) config('analytics.dashboard.enabled')) {
            return false;
        }

        $authorization = config('analytics.dashboard.authorization');

        if (! is_string($authorization) || $authorization === '') {
            return false;
        }

        if (class_exists($authorization)) {
            $checker = app($authorization);

            if (is_callable($checker)) {
                return (bool) $checker($user);
            }
        }

        return Gate::forUser($user)->allows($authorization);
    }
}
