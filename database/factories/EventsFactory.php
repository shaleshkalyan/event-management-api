<?php

namespace Database\Factories;

use App\Models\Events;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class EventsFactory extends Factory
{
    protected $model = Events::class;

    public function definition(): array
    {
        $isRecurring = fake()->boolean(20);
        $recurrenceType = $isRecurring ? fake()->randomElement(['weekly', 'monthly']) : null;
        $date = fake()->dateTimeBetween('+1 week', '+6 months');

        return [
            'title' => fake()->sentence(3) . ' Tech Meetup',
            'description' => fake()->paragraph(),
            'date' => $date,
            'venue' => fake()->address(),
            'capacity' => fake()->numberBetween(20, 90),
            'is_recurring' => $isRecurring,
            'recurrence_type' => $recurrenceType,
        ];
    }

    // State for expired events for testing cleanup command
    public function expired(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => Carbon::now()->subDays(fake()->numberBetween(1, 30)),
            ];
        });
    }
}
