<?php

namespace Database\Factories;

use App\Models\CatechesisSeason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatechesisSeason>
 */
class CatechesisSeasonFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startYear = fake()->unique()->numberBetween(2020, 2090);

        return [
            'name' => $startYear.'-'.($startYear + 1),
            'is_open' => false,
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_open' => true,
        ]);
    }
}
