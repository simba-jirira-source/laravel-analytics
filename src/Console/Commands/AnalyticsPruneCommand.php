<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Console\Commands;

use Illuminate\Console\Command;
use LaravelAnalytics\LaravelAnalytics\Services\AnalyticsPruner;

class AnalyticsPruneCommand extends Command
{
    /**
     * The command signature.
     */
    protected $signature = 'analytics:prune
                            {--days= : Override the configured retention period in days}';

    /**
     * The command description.
     */
    protected $description = 'Prune analytics records older than the configured retention period.';

    public function handle(AnalyticsPruner $pruner): int
    {
        /** @var string|null $daysOption */
        $daysOption = $this->option('days');

        if ($daysOption !== null && $daysOption !== '' && ! is_numeric($daysOption)) {
            $this->components->error('The --days option must be a non-negative integer.');

            return self::FAILURE;
        }

        $days = $daysOption !== null && $daysOption !== '' ? (int) $daysOption : null;
        $cutoff = $pruner->resolveCutoff($days);
        $results = $pruner->prune($days);

        $this->components->info("Pruned analytics records older than {$cutoff->toDateTimeString()}.");

        $this->line("Page views removed: {$results['page_views']}");
        $this->line("Visitors removed: {$results['visitors']}");
        $this->line("Errors removed: {$results['errors']}");
        $this->line("Expired IP bans deactivated: {$results['deactivated_ip_bans']}");
        $this->line("IP ban records removed: {$results['ip_bans']}");

        return self::SUCCESS;
    }
}
