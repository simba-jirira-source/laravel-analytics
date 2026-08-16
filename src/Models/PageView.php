<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SimbaJirira\LaravelAnalytics\Database\Factories\PageViewFactory;

class PageView extends Model
{
    /** @use HasFactory<PageViewFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $table = 'analytics_page_views';

    protected $fillable = [
        'visitor_id',
        'visitor_hash',
        'route_name',
        'path',
        'method',
        'referrer_host',
        'referrer_url',
        'status_code',
        'duration_ms',
        'user_id',
        'viewed_at',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'created_at' => 'datetime',
            'status_code' => 'integer',
            'duration_ms' => 'integer',
            'user_id' => 'integer',
            'visitor_id' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Visitor, $this>
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class, 'visitor_id');
    }

    protected static function newFactory(): PageViewFactory
    {
        return PageViewFactory::new();
    }
}
