<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Support;

use Illuminate\Http\Request;
use LaravelAnalytics\LaravelAnalytics\Contracts\VisitorIdentifier;

class DefaultVisitorIdentifier implements VisitorIdentifier
{
    public function identify(Request $request): string
    {
        $salt = (string) (config('analytics.privacy.hash_salt') ?? config('app.key'));

        $components = [
            $salt,
            $request->ip() ?? '',
        ];

        if ((bool) config('analytics.privacy.collect_user_agent')) {
            $components[] = $request->userAgent() ?? '';
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

        $salt = (string) (config('analytics.privacy.hash_salt') ?? config('app.key'));

        return hash('sha256', $salt.'|ip|'.$ip);
    }
}
