<?php

declare(strict_types=1);

use SimbaJirira\LaravelAnalytics\Models\PageView;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsPruner;

beforeEach(function () {
    config([
        'analytics.retention.days' => 30,
        'analytics.retention.chunk_size' => 3,
        'analytics.retention.prune_page_views' => true,
        'analytics.retention.prune_visitors' => true,
        'analytics.retention.prune_errors' => true,
        'analytics.retention.prune_ip_bans' => true,
    ]);

    $this->pruner = app(AnalyticsPruner::class);
});

it('deletes stale page views using bounded batches', function () {
    PageView::factory()->count(7)->create(['viewed_at' => now()->subDays(120)]);

    $results = $this->pruner->prune();

    expect($results['page_views'])->toBe(7)
        ->and(PageView::query()->count())->toBe(0);
});
