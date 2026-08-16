<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SimbaJirira\LaravelAnalytics\Models\IpBan;

/**
 * @extends Factory<IpBan>
 */
class IpBanFactory extends Factory
{
    protected $model = IpBan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip_address' => fake()->ipv4(),
            'reason' => fake()->optional()->sentence(),
            'is_active' => true,
            'banned_at' => now(),
            'expires_at' => null,
            'banned_by' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'is_active' => true,
            'expires_at' => now()->subDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}
