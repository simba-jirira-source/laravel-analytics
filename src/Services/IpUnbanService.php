<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Services;

use LaravelAnalytics\LaravelAnalytics\Models\IpBan;
use LaravelAnalytics\LaravelAnalytics\Support\IpAddressValidator;

class IpUnbanService
{
    public function __construct(
        protected IpAddressValidator $validator,
    ) {}

    public function unban(string $ip): bool
    {
        $normalizedIp = $this->validator->validate($ip);

        return IpBan::query()
            ->where('ip_address', $normalizedIp)
            ->where('is_active', true)
            ->update(['is_active' => false]) > 0;
    }
}
