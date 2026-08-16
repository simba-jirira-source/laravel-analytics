<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelAnalytics\LaravelAnalytics\Models\IpBan;
use LaravelAnalytics\LaravelAnalytics\Support\IpAddressNormalizer;
use LaravelAnalytics\LaravelAnalytics\Support\IpAddressValidator;
use LaravelAnalytics\LaravelAnalytics\Support\RequestExclusionChecker;
use Symfony\Component\HttpFoundation\Response;

class EnforceIpBanMiddleware
{
    public function __construct(
        protected RequestExclusionChecker $exclusionChecker,
        protected IpAddressNormalizer $normalizer,
        protected IpAddressValidator $validator,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->exclusionChecker->isIpBanningEnabled()) {
            return $next($request);
        }

        if ($this->exclusionChecker->shouldBypassIpBan($request)) {
            return $next($request);
        }

        $clientIp = $request->ip();

        if ($clientIp === null || $clientIp === '') {
            return $next($request);
        }

        $normalizedIp = $this->normalizer->normalize($clientIp);

        if ($normalizedIp === '' || ! $this->validator->isValid($normalizedIp)) {
            return $next($request);
        }

        if (IpBan::findActiveForIp($normalizedIp) !== null) {
            return response('', $this->resolveBlockedStatus());
        }

        return $next($request);
    }

    protected function resolveBlockedStatus(): int
    {
        $status = (int) config('analytics.ip_banning.blocked_status', 403);

        if ($status < 400 || $status > 599) {
            return 403;
        }

        return $status;
    }
}
