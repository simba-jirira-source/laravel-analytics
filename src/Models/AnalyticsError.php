<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use LaravelAnalytics\LaravelAnalytics\Database\Factories\AnalyticsErrorFactory;

/**
 * @property int $id
 * @property string $fingerprint
 * @property string $exception_class
 * @property string $message
 * @property string|null $route_name
 * @property string|null $path
 * @property string|null $method
 * @property int|null $status_code
 * @property string|null $file
 * @property int|null $line
 * @property Carbon $first_occurred_at
 * @property Carbon $last_occurred_at
 * @property int $occurrence_count
 */
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
