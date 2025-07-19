<?php

namespace Database\Seeders;

use App\Models\Events;
use App\Models\EventTickets;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 events
        $events = Events::factory()
            ->count(10)
            ->state(fn() => ['capacity' => rand(1, 7)])
            ->create();

        // Assign statuses to events
        $events->each(function ($event, $index) {
            if ($index > 7) {
                // Mark the last 3 events as recurring
                $event->update(['is_recurring' => true, 'recurrence_type' => 'monthly']);
            } elseif ($index < 4) {
                // Mark the next 3 events as expired
                $event->update(['date' => Carbon::today()->subDays(rand(1, 10))]);
            }
        });

        // Create tickets for each event
        $events->each(function ($event) {
            $capacity = $event->capacity;
            $regularQty = (int) floor($capacity * 0.7);
            $vipQty = $capacity - $regularQty;

            // Create Regular ticket if not exists
            EventTickets::firstOrCreate(
                [
                    'event_id' => $event->id,
                    'type'     => 'Regular',
                ],
                [
                    'price'     => rand(1, 4) * 50,
                    'quantity'  => $regularQty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Create VIP ticket if not exists
            EventTickets::firstOrCreate(
                [
                    'event_id' => $event->id,
                    'type'     => 'VIP',
                ],
                [
                    'price'     => rand(1, 4) * 100,
                    'quantity'  => $vipQty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        });
    }
}
