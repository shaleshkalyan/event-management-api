<?php

namespace Database\Factories;

use App\Models\EventTickets;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Events;

class EventTicketsFactory extends Factory
{
    protected $model = EventTickets::class;

    public function definition(): array
    {
        return [
            'event_id' => Events::factory(),
            'type' => fake()->randomElement(['Regular', 'VIP']),
            'price' => fake()->randomElement([100, 200, 300, 400, 500]),
            'quantity' => fake()->numberBetween(3, 5),
        ];
    }

    public function regular(int $qty, int $price = 50): static
    {
        return $this->state([
            'type'     => 'Regular',
            'price'    => $price,
            'quantity' => $qty,
        ]);
    }

    public function vip(int $qty, int $price = 100): static
    {
        return $this->state([
            'type'     => 'VIP',
            'price'    => $price,
            'quantity' => $qty,
        ]);
    }
}
