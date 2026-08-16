<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use SimbaJirira\LaravelAnalytics\Models\AnalyticsError;
use SimbaJirira\LaravelAnalytics\Models\IpBan;
use SimbaJirira\LaravelAnalytics\Models\PageView;
use SimbaJirira\LaravelAnalytics\Models\Visitor;

class AnalyticsPruner
{
    /**
     * @return array{
     *     page_views: int,
     *     visitors: int,
     *     errors: int,
     *     ip_bans: int,
     *     deactivated_ip_bans: int
     * }
     */
    public function prune(?int $days = null): array
    {
        $cutoff = $this->resolveCutoff($days);

        return [
            'page_views' => $this->prunePageViews($cutoff),
            'visitors' => $this->pruneVisitors($cutoff),
            'errors' => $this->pruneErrors($cutoff),
            'deactivated_ip_bans' => $this->deactivateExpiredIpBans(),
            'ip_bans' => $this->pruneIpBans($cutoff),
        ];
    }

    public function resolveCutoff(?int $days = null): Carbon
    {
        $retentionDays = $days ?? (int) config('analytics.retention.days', 90);

        return Carbon::now()->subDays(max(0, $retentionDays));
    }

    protected function prunePageViews(Carbon $cutoff): int
    {
        if (! (bool) config('analytics.retention.prune_page_views', true)) {
            return 0;
        }

        return $this->deleteInChunks(
            PageView::query()->where('viewed_at', '<', $cutoff),
        );
    }

    protected function pruneVisitors(Carbon $cutoff): int
    {
        if (! (bool) config('analytics.retention.prune_visitors', true)) {
            return 0;
        }

        $deleted = 0;

        do {
            $ids = Visitor::query()
                ->where('last_seen_at', '<', $cutoff)
                ->whereDoesntHave('pageViews', function (Builder $query) use ($cutoff): void {
                    $query->where('viewed_at', '>=', $cutoff);
                })
                ->limit($this->chunkSize())
                ->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            $deleted += Visitor::query()->whereIn('id', $ids)->delete();
        } while ($ids->count() === $this->chunkSize());

        return $deleted;
    }

    protected function pruneErrors(Carbon $cutoff): int
    {
        if (! (bool) config('analytics.retention.prune_errors', true)) {
            return 0;
        }

        return $this->deleteInChunks(
            AnalyticsError::query()->where('last_occurred_at', '<', $cutoff),
        );
    }

    protected function deactivateExpiredIpBans(): int
    {
        if (! (bool) config('analytics.retention.prune_ip_bans', true)) {
            return 0;
        }

        return IpBan::query()
            ->where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['is_active' => false]);
    }

    protected function pruneIpBans(Carbon $cutoff): int
    {
        if (! (bool) config('analytics.retention.prune_ip_bans', true)) {
            return 0;
        }

        return $this->deleteInChunks(
            IpBan::query()->where(function (Builder $query) use ($cutoff): void {
                $query->where(function (Builder $query) use ($cutoff): void {
                    $query->whereNotNull('expires_at')
                        ->where('expires_at', '<', $cutoff);
                })->orWhere(function (Builder $query) use ($cutoff): void {
                    $query->where('is_active', false)
                        ->where('banned_at', '<', $cutoff);
                });
            }),
        );
    }

    /**
     * @param  Builder<*>  $query
     */
    protected function deleteInChunks(Builder $query): int
    {
        $deleted = 0;

        do {
            $count = (clone $query)->limit($this->chunkSize())->delete();
            $deleted += $count;
        } while ($count === $this->chunkSize());

        return $deleted;
    }

    protected function chunkSize(): int
    {
        return max(1, (int) config('analytics.retention.chunk_size', 1000));
    }
}
