<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaravelAnalytics\LaravelAnalytics\Database\Factories\IpBanFactory;

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

    protected static function newFactory(): IpBanFactory
    {
        return IpBanFactory::new();
    }
}
