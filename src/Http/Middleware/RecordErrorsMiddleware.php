<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelAnalytics\LaravelAnalytics\Contracts\ErrorRecorder;
use LaravelAnalytics\LaravelAnalytics\Support\RequestExclusionChecker;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RecordErrorsMiddleware
{
    public function __construct(
        protected RequestExclusionChecker $exclusionChecker,
        protected ErrorRecorder $errorRecorder,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->exclusionChecker->isErrorTrackingEnabled()) {
            return $next($request);
        }

        try {
            return $next($request);
        } catch (Throwable $throwable) {
            $this->recordSafely($request, $throwable);

            throw $throwable;
        }
    }

    protected function recordSafely(Request $request, Throwable $throwable): void
    {
        try {
            $this->errorRecorder->record($throwable, $request);
        } catch (Throwable) {
            // Analytics error recording must never replace the original application exception.
        }
    }
}
