<?php

namespace Database\Factories;

use App\Models\AgendaItem;
use App\Models\Agenda;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AgendaItem>
 */
class AgendaItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AgendaItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+2 months');
        $endDate = clone $startDate;
        $endDate->modify('+2 hours');

        return [
            'agenda_id' => Agenda::factory(),
            'uid' => $this->faker->uuid(),
            'title' => $this->faker->randomElement([
                'Zondagse Dienst',
                'Middagdienst',
                'Avonddienst',
                'Gebedsdienst',
                'Bijbelstudie',
                'Jeugddienst',
            ]),
            'description' => $this->faker->sentence(),
            'location' => 'HHG Rijssen',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Indicate that the agenda item is in the past.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the agenda item is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => $this->faker->dateTimeBetween('now', '+3 months'),
        ]);
    }
}
