<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelAnalytics\LaravelAnalytics\Models\AnalyticsError;

/**
 * @extends Factory<AnalyticsError>
 */
class AnalyticsErrorFactory extends Factory
{
    protected $model = AnalyticsError::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $occurredAt = fake()->dateTimeBetween('-14 days', 'now');
        $message = fake()->sentence();

        return [
            'fingerprint' => hash('sha256', $message),
            'exception_class' => 'RuntimeException',
            'message' => $message,
            'route_name' => fake()->optional()->slug(2),
            'path' => '/'.fake()->slug(3),
            'method' => 'GET',
            'status_code' => 500,
            'file' => fake()->filePath(),
            'line' => fake()->numberBetween(1, 500),
            'first_occurred_at' => $occurredAt,
            'last_occurred_at' => $occurredAt,
            'occurrence_count' => 1,
        ];
    }
}
