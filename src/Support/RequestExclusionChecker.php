<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestExclusionChecker
{
    public function isTrackingEnabled(): bool
    {
        return (bool) config('analytics.enabled')
            && (bool) config('analytics.tracking.traffic');
    }

    public function shouldTrackRequest(Request $request): bool
    {
        if (! $this->isTrackingEnabled()) {
            return false;
        }

        if ($this->isIgnoredMethod($request->method())) {
            return false;
        }

        if ($this->isIgnoredPath($request->path())) {
            return false;
        }

        $routeName = $request->route()?->getName();

        if ($routeName !== null && $this->isIgnoredRouteName($routeName)) {
            return false;
        }

        return true;
    }

    public function shouldRecordStatus(int $statusCode): bool
    {
        /** @var list<int> $excluded */
        $excluded = config('analytics.excluded_status_codes', []);

        return ! in_array($statusCode, $excluded, true);
    }

    public function isIgnoredMethod(string $method): bool
    {
        /** @var list<string> $ignored */
        $ignored = config('analytics.ignored.methods', []);

        return in_array(strtoupper($method), array_map('strtoupper', $ignored), true);
    }

    public function isIgnoredPath(string $path): bool
    {
        $normalizedPath = trim($path, '/') ?: '';

        /** @var list<string> $patterns */
        $patterns = config('analytics.ignored.paths', []);

        foreach ($patterns as $pattern) {
            $normalizedPattern = trim($pattern, '/');

            if ($normalizedPattern === $normalizedPath) {
                return true;
            }

            if (Str::is($normalizedPattern, $normalizedPath)) {
                return true;
            }
        }

        return false;
    }

    public function isIgnoredRouteName(string $routeName): bool
    {
        /** @var list<string> $patterns */
        $patterns = config('analytics.ignored.route_names', []);

        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }
}
