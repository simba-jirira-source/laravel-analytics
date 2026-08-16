<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class RequestExclusionChecker
{
    public function isTrackingEnabled(): bool
    {
        return (bool) config('analytics.enabled')
            && (bool) config('analytics.tracking.traffic');
    }

    public function isErrorTrackingEnabled(): bool
    {
        return (bool) config('analytics.enabled')
            && (bool) config('analytics.tracking.errors');
    }

    public function isIpBanningEnabled(): bool
    {
        return (bool) config('analytics.enabled')
            && (bool) config('analytics.ip_banning.enabled');
    }

    public function shouldBypassIpBan(Request $request): bool
    {
        if ($this->isIgnoredMethod($request->method())) {
            return true;
        }

        if ($this->matchesIpBanBypassPath($request->path())) {
            return true;
        }

        $routeName = $request->route()?->getName();

        if ($routeName !== null && $this->matchesIpBanBypassRouteName($routeName)) {
            return true;
        }

        return false;
    }

    protected function matchesIpBanBypassPath(string $path): bool
    {
        $normalizedPath = trim($path, '/') ?: '';

        /** @var list<string> $patterns */
        $patterns = config('analytics.ip_banning.bypass_paths', []);

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

    protected function matchesIpBanBypassRouteName(string $routeName): bool
    {
        /** @var list<string> $patterns */
        $patterns = config('analytics.ip_banning.bypass_route_names', []);

        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }

    public function shouldRecordError(Request $request, Throwable $throwable): bool
    {
        if (! $this->isErrorTrackingEnabled()) {
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

        if ($this->isPackageRecorderFailure($throwable)) {
            return false;
        }

        return true;
    }

    protected function isPackageRecorderFailure(Throwable $throwable): bool
    {
        $file = str_replace('\\', '/', $throwable->getFile());

        return str_contains($file, '/Services/AnalyticsErrorRecorder.php')
            || str_contains($file, '/Http/Middleware/RecordErrorsMiddleware.php');
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
