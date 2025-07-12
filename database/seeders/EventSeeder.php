<?php

namespace Database\Seeders;

use App\Models\Events;
use App\Models\EventTickets;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 regular events, each with 2 ticket types
        Events::factory()->count(5)->create()->each(function ($event) {
            EventTickets::factory()->count(2)->create([
                'event_id' => $event->id,
                'type' => 'Regular',
            ]);
            EventTickets::factory()->count(1)->create([
                'event_id' => $event->id,
                'type' => 'VIP',
            ]);
        });

        // Create 2 recurring events
        Events::factory()->count(2)->create([
            'is_recurring' => true,
            'recurrence_type' => 'monthly'
        ])->each(function ($event) {
            EventTickets::factory()->count(2)->create(['event_id' => $event->id]);
        });

        // Create 3 expired events for testing cleanup command
        Events::factory()->count(3)->expired()->create()->each(function ($event) {
            EventTickets::factory()->count(1)->create(['event_id' => $event->id]);
        });
    }
}
