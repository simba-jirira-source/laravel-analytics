<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Support;

use Illuminate\Support\Carbon;
use InvalidArgumentException;

readonly class DashboardDateRange
{
    public function __construct(
        public Carbon $from,
        public Carbon $to,
    ) {}

    public static function resolve(?string $from = null, ?string $to = null): self
    {
        $resolvedTo = self::parseDate($to) ?? Carbon::now()->endOfDay();
        $resolvedFrom = self::parseDate($from) ?? $resolvedTo->copy()->subDays(30)->startOfDay();

        if ($resolvedFrom->greaterThan($resolvedTo)) {
            throw new InvalidArgumentException('The start date must be before the end date.');
        }

        return new self($resolvedFrom->startOfDay(), $resolvedTo->endOfDay());
    }

    public static function resolveOrDefault(?string $from = null, ?string $to = null): self
    {
        try {
            return self::resolve($from, $to);
        } catch (InvalidArgumentException) {
            return self::resolve(null, null);
        }
    }

    protected static function parseDate(?string $value): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return Carbon::parse($value);
    }
}
