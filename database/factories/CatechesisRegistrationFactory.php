<?php

namespace Database\Factories;

use App\Enums\CatechesisGroup;
use App\Models\CatechesisRegistration;
use App\Models\CatechesisSeason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatechesisRegistration>
 */
class CatechesisRegistrationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'season_id' => CatechesisSeason::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('06########'),
            'group' => fake()->randomElement(CatechesisGroup::cases()),
        ];
    }
}
