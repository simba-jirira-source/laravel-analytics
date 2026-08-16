<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Support;

class AnalyticsHashSalt
{
    public function resolve(): string
    {
        $configured = config('analytics.privacy.hash_salt');

        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        return (string) config('app.key');
    }
}
