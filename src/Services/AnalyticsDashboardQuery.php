<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Services;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use SimbaJirira\LaravelAnalytics\Models\AnalyticsError;
use SimbaJirira\LaravelAnalytics\Models\IpBan;
use SimbaJirira\LaravelAnalytics\Models\PageView;
use SimbaJirira\LaravelAnalytics\Support\DashboardDateRange;
use SimbaJirira\LaravelAnalytics\Support\DatabaseSqlHelper;
use stdClass;

class AnalyticsDashboardQuery
{
    /**
     * @return array{
     *     page_views: int,
     *     unique_visitors: int,
     *     visits: int,
     *     errors: int,
     *     active_bans: int
     * }
     */
    public function overviewMetrics(DashboardDateRange $range): array
    {
        $ttl = (int) config('analytics.dashboard.cache_ttl', 0);

        if ($ttl <= 0) {
            return $this->resolveOverviewMetrics($range);
        }

        $cacheKey = sprintf(
            'analytics.dashboard.overview.%s.%s',
            $range->from->timestamp,
            $range->to->timestamp,
        );

        /** @var array{page_views: int, unique_visitors: int, visits: int, errors: int, active_bans: int} $metrics */
        $metrics = Cache::remember($cacheKey, $ttl, fn (): array => $this->resolveOverviewMetrics($range));

        return $metrics;
    }

    /**
     * @return array{
     *     page_views: int,
     *     unique_visitors: int,
     *     visits: int,
     *     errors: int,
     *     active_bans: int
     * }
     */
    protected function resolveOverviewMetrics(DashboardDateRange $range): array
    {
        $pageViewsQuery = PageView::query()->whereBetween('viewed_at', [$range->from, $range->to]);

        return [
            'page_views' => (clone $pageViewsQuery)->count(),
            'unique_visitors' => PageView::query()
                ->whereBetween('viewed_at', [$range->from, $range->to])
                ->distinct('visitor_hash')
                ->count('visitor_hash'),
            'visits' => $this->distinctVisitorDayCount(clone $pageViewsQuery),
            'errors' => AnalyticsError::query()
                ->whereBetween('last_occurred_at', [$range->from, $range->to])
                ->count(),
            'active_bans' => IpBan::query()->active()->count(),
        ];
    }

    /**
     * @return Collection<int, stdClass>
     */
    public function trafficTrend(DashboardDateRange $range): Collection
    {
        /** @var Connection $connection */
        $connection = PageView::query()->getConnection();
        /** @var literal-string $dateExpression */
        $dateExpression = DatabaseSqlHelper::trafficTrendDateExpression($connection);

        return PageView::query()
            ->selectRaw("{$dateExpression}, COUNT(*) as total")
            ->whereBetween('viewed_at', [$range->from, $range->to])
            ->groupBy('date')
            ->orderBy('date')
            ->toBase()
            ->get();
    }

    /**
     * @return Collection<int, stdClass>
     */
    public function topPages(DashboardDateRange $range, int $limit = 10): Collection
    {
        return PageView::query()
            ->select('path', DB::raw('COUNT(*) as views'))
            ->whereBetween('viewed_at', [$range->from, $range->to])
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit($limit)
            ->toBase()
            ->get();
    }

    /**
     * @return Collection<int, stdClass>
     */
    public function topReferrers(DashboardDateRange $range, int $limit = 10): Collection
    {
        return PageView::query()
            ->select('referrer_host', DB::raw('COUNT(*) as views'))
            ->whereBetween('viewed_at', [$range->from, $range->to])
            ->whereNotNull('referrer_host')
            ->where('referrer_host', '!=', '')
            ->groupBy('referrer_host')
            ->orderByDesc('views')
            ->limit($limit)
            ->toBase()
            ->get();
    }

    /**
     * @return Collection<int, stdClass>
     */
    public function statusBreakdown(DashboardDateRange $range): Collection
    {
        return PageView::query()
            ->select('status_code', DB::raw('COUNT(*) as total'))
            ->whereBetween('viewed_at', [$range->from, $range->to])
            ->groupBy('status_code')
            ->orderBy('status_code')
            ->toBase()
            ->get();
    }

    /**
     * @return Builder<AnalyticsError>
     */
    public function recentErrorsQuery(DashboardDateRange $range): Builder
    {
        return AnalyticsError::query()
            ->whereBetween('last_occurred_at', [$range->from, $range->to])
            ->orderByDesc('last_occurred_at');
    }

    /**
     * @return Builder<IpBan>
     */
    public function ipBansQuery(): Builder
    {
        return IpBan::query()->orderByDesc('banned_at');
    }

    /**
     * @param  Builder<PageView>  $query
     */
    protected function distinctVisitorDayCount(Builder $query): int
    {
        /** @var Connection $connection */
        $connection = $query->getConnection();
        /** @var literal-string $expression */
        $expression = DatabaseSqlHelper::distinctVisitorDayCountExpression($connection);

        return (int) (clone $query)->toBase()
            ->selectRaw($expression)
            ->value('total');
    }
}
