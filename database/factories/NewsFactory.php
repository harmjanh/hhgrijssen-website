<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(rand(4, 8));

        return [
            'title' => $title,
            'description' => substr(fake()->paragraph(), 0, 200),
            'content' => collect(range(1, rand(3, 6)))
                ->map(fn () => fake()->paragraph(rand(4, 10)))
                ->join("\n\n"),
            'image' => fake()->boolean(80) ? fake()->image(
                storage_path('app/public/news'),
                800,
                600,
                null,
                false
            ) : null,
            'visible_from' => now(),
            'visible_until' => null,
        ];
    }
}
