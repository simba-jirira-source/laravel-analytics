<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Support;

use Illuminate\Database\Connection;

class DatabaseSqlHelper
{
    public static function dateExpression(Connection $connection, string $column): string
    {
        return match ($connection->getDriverName()) {
            'sqlite' => "date({$column})",
            default => "DATE({$column})",
        };
    }

    public static function distinctVisitorDayCountExpression(Connection $connection, string $hashColumn = 'visitor_hash', string $dateColumn = 'viewed_at'): string
    {
        $dateExpression = self::dateExpression($connection, $dateColumn);

        return match ($connection->getDriverName()) {
            'sqlite' => "COUNT(DISTINCT {$hashColumn} || '|' || {$dateExpression}) as total",
            default => "COUNT(DISTINCT CONCAT({$hashColumn}, '|', {$dateExpression})) as total",
        };
    }

    public static function trafficTrendDateExpression(Connection $connection, string $column = 'viewed_at'): string
    {
        return self::dateExpression($connection, $column).' as date';
    }
}
