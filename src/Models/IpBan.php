<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use SimbaJirira\LaravelAnalytics\Database\Factories\IpBanFactory;

/**
 * @property Carbon $banned_at
 * @property Carbon|null $expires_at
 * @property bool $is_active
 * @property string $ip_address
 * @property string|null $reason
 */
class IpBan extends Model
{
    /** @use HasFactory<IpBanFactory> */
    use HasFactory;

    protected $table = 'analytics_ip_bans';

    protected $fillable = [
        'ip_address',
        'reason',
        'is_active',
        'banned_at',
        'expires_at',
        'banned_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'banned_at' => 'datetime',
            'expires_at' => 'datetime',
            'banned_by' => 'integer',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? false;
    }

    public function isCurrentlyActive(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }

    /**
     * @param  Builder<IpBan>  $query
     * @return Builder<IpBan>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public static function findActiveForIp(string $normalizedIp): ?self
    {
        return static::query()
            ->where('ip_address', $normalizedIp)
            ->active()
            ->first();
    }

    protected static function newFactory(): IpBanFactory
    {
        return IpBanFactory::new();
    }
}
