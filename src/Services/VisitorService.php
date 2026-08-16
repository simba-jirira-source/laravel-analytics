<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use SimbaJirira\LaravelAnalytics\Contracts\VisitorIdentifier;
use SimbaJirira\LaravelAnalytics\Models\Visitor;

class VisitorService
{
    public function __construct(
        protected VisitorIdentifier $visitorIdentifier,
    ) {}

    public function identify(Request $request): string
    {
        return $this->visitorIdentifier->identify($request);
    }

    public function upsertFromRequest(Request $request, Carbon $viewedAt): Visitor
    {
        return $this->upsert(
            $request,
            $this->identify($request),
            $viewedAt,
        );
    }

    public function upsert(Request $request, string $visitorHash, Carbon $viewedAt): Visitor
    {
        $attributes = $this->buildAttributes($request, $viewedAt);

        $visitor = Visitor::query()->firstOrCreate(
            ['visitor_hash' => $visitorHash],
            $attributes,
        );

        if ($visitor->wasRecentlyCreated) {
            return $visitor;
        }

        $visitor->forceFill([
            'last_seen_at' => $viewedAt,
            'user_id' => $attributes['user_id'] ?? $visitor->user_id,
            'ip_address' => $attributes['ip_address'],
            'ip_hash' => $attributes['ip_hash'],
            'user_agent' => $attributes['user_agent'],
        ])->save();

        return $visitor->refresh();
    }

    /**
     * @return array{
     *     first_seen_at: Carbon,
     *     last_seen_at: Carbon,
     *     user_id: ?int,
     *     ip_address: ?string,
     *     ip_hash: ?string,
     *     user_agent: ?string
     * }
     */
    protected function buildAttributes(Request $request, Carbon $viewedAt): array
    {
        return [
            'first_seen_at' => $viewedAt,
            'last_seen_at' => $viewedAt,
            'user_id' => $this->resolveUserId($request),
            'ip_address' => $this->resolveRawIp($request),
            'ip_hash' => $this->visitorIdentifier->hashIp($request->ip()),
            'user_agent' => $this->resolveUserAgent($request),
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
}
