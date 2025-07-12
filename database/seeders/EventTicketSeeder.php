<?php

namespace Database\Seeders;

use App\Models\Events;
use App\Models\EventTickets;
use Illuminate\Database\Seeder;

class EventTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Events::all();
        foreach ($events as $event) {
            EventTickets::factory()->count(2)->create(['event_id' => $event->id]);
        }
    }
}
