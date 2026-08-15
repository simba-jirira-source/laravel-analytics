<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use LaravelAnalytics\LaravelAnalytics\Contracts\AnalyticsRecorder;
use LaravelAnalytics\LaravelAnalytics\Contracts\VisitorIdentifier;
use LaravelAnalytics\LaravelAnalytics\Models\PageView;
use LaravelAnalytics\LaravelAnalytics\Models\Visitor;
use LaravelAnalytics\LaravelAnalytics\Support\RequestExclusionChecker;
use Symfony\Component\HttpFoundation\Response;

class PageViewRecorder implements AnalyticsRecorder
{
    public function __construct(
        protected RequestExclusionChecker $exclusionChecker,
        protected VisitorIdentifier $visitorIdentifier,
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
        $visitorHash = $this->visitorIdentifier->identify($request);
        $visitor = $this->resolveVisitor($request, $visitorHash, $viewedAt);
        [$referrerHost, $referrerUrl] = $this->resolveReferrer($request);

        PageView::query()->create([
            'visitor_id' => $visitor->id,
            'visitor_hash' => $visitorHash,
            'route_name' => $request->route()?->getName(),
            'path' => $this->resolvePath($request),
            'method' => strtoupper($request->method()),
            'referrer_host' => $referrerHost,
            'referrer_url' => $referrerUrl,
            'status_code' => $response->getStatusCode(),
            'duration_ms' => max(0, $durationMs),
            'user_id' => $this->resolveUserId($request),
            'viewed_at' => $viewedAt,
            'created_at' => $viewedAt,
        ]);
    }

    protected function resolveVisitor(Request $request, string $visitorHash, Carbon $viewedAt): Visitor
    {
        $attributes = [
            'first_seen_at' => $viewedAt,
            'last_seen_at' => $viewedAt,
            'user_id' => $this->resolveUserId($request),
            'ip_address' => $this->resolveRawIp($request),
            'ip_hash' => $this->resolveIpHash($request),
            'user_agent' => $this->resolveUserAgent($request),
        ];

        $visitor = Visitor::query()->firstOrCreate(
            ['visitor_hash' => $visitorHash],
            $attributes,
        );

        if (! $visitor->wasRecentlyCreated) {
            $visitor->forceFill(['last_seen_at' => $viewedAt])->save();
        }

        return $visitor;
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

        $host = parse_url($referrer, PHP_URL_HOST);

        return [
            is_string($host) ? $host : null,
            $referrer,
        ];
    }

    protected function resolveUserAgent(Request $request): ?string
    {
        if (! (bool) config('analytics.privacy.collect_user_agent')) {
            return null;
        }

        return $request->userAgent();
    }

    protected function resolveUserId(Request $request): ?int
    {
        if (! (bool) config('analytics.privacy.track_authenticated_users')) {
            return null;
        }

        $user = $request->user();

        if ($user === null) {
            return null;
        }

        $identifier = $user->getAuthIdentifier();

        return is_numeric($identifier) ? (int) $identifier : null;
    }

    protected function resolveRawIp(Request $request): ?string
    {
        if (! (bool) config('analytics.privacy.store_raw_ip')) {
            return null;
        }

        return $request->ip();
    }

    protected function resolveIpHash(Request $request): ?string
    {
        return $this->visitorIdentifier->hashIp($request->ip());
    }
}
