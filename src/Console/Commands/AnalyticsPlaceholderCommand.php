<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Console\Commands;

use Illuminate\Console\Command;

class AnalyticsPlaceholderCommand extends Command
{
    /**
     * The command signature.
     */
    protected $signature = 'analytics:placeholder';

    /**
     * The command description.
     */
    protected $description = 'Placeholder Artisan command shipped by the Laravel Analytics package.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->line('Laravel Analytics placeholder command executed.');

        return self::SUCCESS;
    }
}
