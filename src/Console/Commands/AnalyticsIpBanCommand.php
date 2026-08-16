<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use SimbaJirira\LaravelAnalytics\Services\IpBanService;

class AnalyticsIpBanCommand extends Command
{
    /**
     * The command signature.
     */
    protected $signature = 'analytics:ip-ban
                            {ip : The IPv4 or IPv6 address to ban}
                            {--reason= : Optional reason for the ban}
                            {--expires= : Optional expiry datetime (Y-m-d H:i:s)}
                            {--days= : Optional number of days until the ban expires}';

    /**
     * The command description.
     */
    protected $description = 'Ban an exact IPv4 or IPv6 address from accessing the application.';

    public function handle(IpBanService $banService): int
    {
        /** @var string $ip */
        $ip = $this->argument('ip');

        /** @var string|null $reason */
        $reason = $this->option('reason');

        $expiresAt = $this->resolveExpiry();

        try {
            $ban = $banService->ban(
                ip: $ip,
                reason: is_string($reason) && $reason !== '' ? $reason : null,
                expiresAt: $expiresAt,
            );
        } catch (InvalidArgumentException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info("Banned IP address {$ban->ip_address}.");

        if ($ban->expires_at !== null) {
            $this->line("Expires at: {$ban->expires_at->toDateTimeString()}");
        }

        return self::SUCCESS;
    }

    protected function resolveExpiry(): ?Carbon
    {
        /** @var string|null $expires */
        $expires = $this->option('expires');

        /** @var string|null $days */
        $days = $this->option('days');

        if (is_string($expires) && $expires !== '') {
            return Carbon::parse($expires);
        }

        if (is_string($days) && $days !== '' && is_numeric($days)) {
            return now()->addDays((int) $days);
        }

        return null;
    }
}
