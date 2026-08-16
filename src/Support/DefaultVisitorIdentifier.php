<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Support;

use Illuminate\Http\Request;
use SimbaJirira\LaravelAnalytics\Contracts\VisitorIdentifier;

class DefaultVisitorIdentifier implements VisitorIdentifier
{
    public function __construct(
        protected AnalyticsHashSalt $hashSalt,
        protected IpAddressNormalizer $ipNormalizer,
    ) {}

    public function identify(Request $request): string
    {
        $components = [
            $this->hashSalt->resolve(),
            $this->ipNormalizer->normalize($request->ip()),
        ];

        if ((bool) config('analytics.privacy.collect_user_agent')) {
            $components[] = $request->userAgent() ?? '';
        }

        if ((bool) config('analytics.privacy.track_authenticated_users')) {
            $userId = $request->user()?->getAuthIdentifier();

            if ($userId !== null && $userId !== '') {
                $components[] = 'user:'.(string) $userId;
            }
        }

        return hash('sha256', implode('|', $components));
    }

    public function hashIp(?string $ip): ?string
    {
        if ($ip === null || $ip === '') {
            return null;
        }

        if (! (bool) config('analytics.privacy.hash_ips')) {
            return null;
        }

        $normalized = $this->ipNormalizer->normalize($ip);

        return hash('sha256', $this->hashSalt->resolve().'|ip|'.$normalized);
    }
}
