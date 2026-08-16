<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use LaravelAnalytics\LaravelAnalytics\Contracts\AnalyticsRecorder;
use LaravelAnalytics\LaravelAnalytics\Models\PageView;
use LaravelAnalytics\LaravelAnalytics\Support\RequestExclusionChecker;
use Symfony\Component\HttpFoundation\Response;

class PageViewRecorder implements AnalyticsRecorder
{
    public function __construct(
        protected RequestExclusionChecker $exclusionChecker,
        protected VisitorService $visitorService,
    ) {}

    public function record(Request $request, Response $response, int $durationMs): void
    {
        if (! $this->exclusionChecker->shouldTrackRequest($request)) {
            return;
        }

        if (! $this->exclusionChecker->shouldRecordStatus($response->getStatusCode())) {
            return;
        }

        $viewedAt = Carbon::now();

        DB::transaction(function () use ($request, $response, $durationMs, $viewedAt): void {
            $visitor = $this->visitorService->upsertFromRequest($request, $viewedAt);
            [$referrerHost, $referrerUrl] = $this->resolveReferrer($request);

            PageView::query()->create([
                'visitor_id' => $visitor->id,
                'visitor_hash' => $visitor->visitor_hash,
                'route_name' => $request->route()?->getName(),
                'path' => $this->resolvePath($request),
                'method' => strtoupper($request->method()),
                'referrer_host' => $referrerHost,
                'referrer_url' => $referrerUrl,
                'status_code' => $response->getStatusCode(),
                'duration_ms' => max(0, $durationMs),
                'user_id' => $visitor->user_id,
                'viewed_at' => $viewedAt,
                'created_at' => $viewedAt,
            ]);
        });
    }

    protected function resolvePath(Request $request): string
    {
        $path = $request->getPathInfo();

        return $path !== '' ? $path : '/';
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    protected function resolveReferrer(Request $request): array
    {
        if (! (bool) config('analytics.privacy.collect_referrer')) {
            return [null, null];
        }

        $referrer = $request->headers->get('referer');

        if (! is_string($referrer) || $referrer === '') {
            return [null, null];
        }

        $referrerWithoutQuery = strtok($referrer, '?') ?: $referrer;
        $host = parse_url($referrerWithoutQuery, PHP_URL_HOST);

        return [
            is_string($host) ? $host : null,
            $referrerWithoutQuery,
        ];
    }
}
