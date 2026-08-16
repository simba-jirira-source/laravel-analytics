<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use SimbaJirira\LaravelAnalytics\Database\Factories\VisitorFactory;

/**
 * @property int $id
 * @property string $visitor_hash
 * @property Carbon $first_seen_at
 * @property Carbon $last_seen_at
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $ip_hash
 * @property string|null $user_agent
 */
class Visitor extends Model
{
    /** @use HasFactory<VisitorFactory> */
    use HasFactory;

    protected $table = 'analytics_visitors';

    protected $fillable = [
        'visitor_hash',
        'first_seen_at',
        'last_seen_at',
        'user_id',
        'ip_address',
        'ip_hash',
        'user_agent',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'user_id' => 'integer',
        ];
    }

    /**
     * @return HasMany<PageView, $this>
     */
    public function pageViews(): HasMany
    {
        return $this->hasMany(PageView::class, 'visitor_id');
    }

    protected static function newFactory(): VisitorFactory
    {
        return VisitorFactory::new();
    }
}
