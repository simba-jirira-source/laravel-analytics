<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use SimbaJirira\LaravelAnalytics\Models\Visitor;

class VisitorAnalytics
{
    public function uniqueVisitorCount(?Carbon $from = null, ?Carbon $to = null): int
    {
        return Visitor::query()
            ->when($from !== null, fn (Builder $query): Builder => $query->where('last_seen_at', '>=', $from))
            ->when($to !== null, fn (Builder $query): Builder => $query->where('first_seen_at', '<=', $to))
            ->count();
    }

    public function repeatVisitorCount(?Carbon $from = null): int
    {
        return Visitor::query()
            ->when($from !== null, fn (Builder $query): Builder => $query->where('last_seen_at', '>=', $from))
            ->has('pageViews', '>=', 2)
            ->count();
    }

    public function isRepeatVisitor(Visitor $visitor): bool
    {
        return $visitor->pageViews()->count() >= 2;
    }
}
