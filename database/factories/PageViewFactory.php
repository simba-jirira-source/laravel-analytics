<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelAnalytics\LaravelAnalytics\Models\PageView;
use LaravelAnalytics\LaravelAnalytics\Models\Visitor;

/**
 * @extends Factory<PageView>
 */
class PageViewFactory extends Factory
{
    protected $model = PageView::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $viewedAt = fake()->dateTimeBetween('-7 days', 'now');

        return [
            'visitor_id' => Visitor::factory(),
            'visitor_hash' => hash('sha256', fake()->uuid()),
            'route_name' => fake()->optional()->slug(2),
            'path' => '/'.fake()->slug(3),
            'method' => fake()->randomElement(['GET', 'POST', 'PUT', 'PATCH', 'DELETE']),
            'referrer_host' => fake()->optional()->domainName(),
            'referrer_url' => fake()->optional()->url(),
            'status_code' => fake()->randomElement([200, 201, 301, 302, 404, 500]),
            'duration_ms' => fake()->numberBetween(10, 2500),
            'user_id' => null,
            'viewed_at' => $viewedAt,
            'created_at' => $viewedAt,
        ];
    }
}
