<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Tests;

abstract class RetentionTestCase extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'analytics.retention.days' => 90,
            'analytics.retention.prune_page_views' => true,
            'analytics.retention.prune_visitors' => true,
            'analytics.retention.prune_errors' => true,
            'analytics.retention.prune_ip_bans' => true,
        ]);
    }
}
