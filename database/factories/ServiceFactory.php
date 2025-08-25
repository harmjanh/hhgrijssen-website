<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\AgendaItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agenda_item_id' => AgendaItem::factory(),
            'pastor' => $this->faker->randomElement([
                'Ds. J. van der Graaf',
                'Ds. M. van der Veen',
                'Ds. A. van der Berg',
                'Ds. P. van der Meulen',
                'Ds. R. van der Wal',
            ]),
            'liturgy' => $this->faker->randomElement([
                '<ul><li>Votum & Groet</li><li>Psalm 100</li><li>Gebed</li><li>Schriftlezing</li><li>Preek</li><li>Gebed</li><li>Psalm 134</li><li>Zegen</li></ul>',
                '<ul><li>Votum & Groet</li><li>Psalm 23</li><li>Gebed</li><li>Schriftlezing</li><li>Preek</li><li>Gebed</li><li>Psalm 103</li><li>Zegen</li></ul>',
                '<ul><li>Votum & Groet</li><li>Psalm 46</li><li>Gebed</li><li>Schriftlezing</li><li>Preek</li><li>Gebed</li><li>Psalm 121</li><li>Zegen</li></ul>',
            ]),
            'youtube_url' => $this->faker->optional(0.7)->url(),
        ];
    }

    /**
     * Indicate that the service is linked to a past agenda item.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'agenda_item_id' => AgendaItem::factory()->state([
                'start_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            ]),
        ]);
    }

    /**
     * Indicate that the service is linked to an upcoming agenda item.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'agenda_item_id' => AgendaItem::factory()->state([
                'start_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            ]),
        ]);
    }

    /**
     * Indicate that the service has a YouTube recording.
     */
    public function withRecording(): static
    {
        return $this->state(fn (array $attributes) => [
            'youtube_url' => 'https://www.youtube.com/watch?v=' . $this->faker->regexify('[A-Za-z0-9]{11}'),
        ]);
    }
}
