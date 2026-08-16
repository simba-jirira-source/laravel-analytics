<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SimbaJirira\LaravelAnalytics\Contracts\AnalyticsRecorder;
use SimbaJirira\LaravelAnalytics\Support\RequestExclusionChecker;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackTrafficMiddleware
{
    public function __construct(
        protected RequestExclusionChecker $exclusionChecker,
        protected AnalyticsRecorder $recorder,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->exclusionChecker->isTrackingEnabled()) {
            return $next($request);
        }

        $startedAt = microtime(true);
        $response = $next($request);

        if ($this->exclusionChecker->shouldTrackRequest($request)) {
            try {
                $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

                $this->recorder->record($request, $response, $durationMs);
            } catch (Throwable) {
                // Never interrupt the application response when analytics persistence fails.
            }
        }

        return $response;
    }
}
