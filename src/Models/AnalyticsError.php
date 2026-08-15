<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaravelAnalytics\LaravelAnalytics\Database\Factories\AnalyticsErrorFactory;

class AnalyticsError extends Model
{
    /** @use HasFactory<AnalyticsErrorFactory> */
    use HasFactory;

    protected $table = 'analytics_errors';

    protected $fillable = [
        'fingerprint',
        'exception_class',
        'message',
        'route_name',
        'path',
        'method',
        'status_code',
        'file',
        'line',
        'first_occurred_at',
        'last_occurred_at',
        'occurrence_count',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'first_occurred_at' => 'datetime',
            'last_occurred_at' => 'datetime',
            'occurrence_count' => 'integer',
            'status_code' => 'integer',
            'line' => 'integer',
        ];
    }

    protected static function newFactory(): AnalyticsErrorFactory
    {
        return AnalyticsErrorFactory::new();
    }
}
