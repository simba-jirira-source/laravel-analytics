<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Services;

use SimbaJirira\LaravelAnalytics\Models\IpBan;
use SimbaJirira\LaravelAnalytics\Support\IpAddressValidator;

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
