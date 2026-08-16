<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Services;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use LaravelAnalytics\LaravelAnalytics\Models\IpBan;
use LaravelAnalytics\LaravelAnalytics\Support\IpAddressValidator;

class IpBanService
{
    public function __construct(
        protected IpAddressValidator $validator,
    ) {}

    public function ban(
        string $ip,
        ?string $reason = null,
        ?DateTimeInterface $expiresAt = null,
        ?int $bannedBy = null,
    ): IpBan {
        $normalizedIp = $this->validator->validate($ip);
        $bannedAt = Carbon::now();

        /** @var IpBan|null $existing */
        $existing = IpBan::query()
            ->where('ip_address', $normalizedIp)
            ->first();

        if ($existing !== null) {
            $existing->forceFill([
                'reason' => $reason,
                'is_active' => true,
                'banned_at' => $bannedAt,
                'expires_at' => $expiresAt !== null ? Carbon::instance($expiresAt) : null,
                'banned_by' => $bannedBy,
            ])->save();

            return $existing->refresh();
        }

        return IpBan::query()->create([
            'ip_address' => $normalizedIp,
            'reason' => $reason,
            'is_active' => true,
            'banned_at' => $bannedAt,
            'expires_at' => $expiresAt !== null ? Carbon::instance($expiresAt) : null,
            'banned_by' => $bannedBy,
        ]);
    }
}
