<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use LaravelAnalytics\LaravelAnalytics\Services\IpUnbanService;

class AnalyticsIpUnbanCommand extends Command
{
    /**
     * The command signature.
     */
    protected $signature = 'analytics:ip-unban {ip : The IPv4 or IPv6 address to unban}';

    /**
     * The command description.
     */
    protected $description = 'Remove an active IP ban for recovery without using the dashboard.';

    public function handle(IpUnbanService $unbanService): int
    {
        /** @var string $ip */
        $ip = $this->argument('ip');

        try {
            $removed = $unbanService->unban($ip);
        } catch (InvalidArgumentException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        if (! $removed) {
            $this->components->warn("No active ban found for {$ip}.");

            return self::SUCCESS;
        }

        $this->components->info("Removed active ban for {$ip}.");

        return self::SUCCESS;
    }
}
