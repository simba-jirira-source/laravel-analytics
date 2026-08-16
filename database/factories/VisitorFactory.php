<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SimbaJirira\LaravelAnalytics\Models\Visitor;

/**
 * @extends Factory<Visitor>
 */
class VisitorFactory extends Factory
{
    protected $model = Visitor::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $seenAt = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'visitor_hash' => hash('sha256', fake()->uuid()),
            'first_seen_at' => $seenAt,
            'last_seen_at' => $seenAt,
            'user_id' => null,
            'ip_address' => null,
            'ip_hash' => hash('sha256', fake()->ipv4()),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
