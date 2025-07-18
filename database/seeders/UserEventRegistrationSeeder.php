<?php

namespace Database\Seeders;

use App\Models\Events;
use App\Models\User;
use App\Models\UserEventRegistration;
use Illuminate\Database\Seeder;

class UserEventRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $events = Events::with('eventTickets')->get();

        foreach ($events as $event) {
            $tickets = $event->eventTickets;
            if ($tickets->isEmpty()) continue;

            // Pick up to 5 random users to attempt registration
            $candidates = $users->random(min(5, $users->count()));

            foreach ($candidates as $user) {
                $ticket = $tickets->random();

                // Skip if already registered for this event & ticket
                if (UserEventRegistration::where([
                    ['user_id', $user->id],
                    ['event_id', $event->id],
                    ['event_ticket_id', $ticket->id],
                ])->exists()) {
                    continue;
                }

                // Count confirmed (registered) bookings per ticket type and per event
                $ticketConfirmedCount = UserEventRegistration::where([
                    ['event_ticket_id', $ticket->id],
                    ['status', 'registered'],
                ])->count();

                $eventConfirmedCount = UserEventRegistration::where([
                    ['event_id', $event->id],
                    ['status', 'registered'],
                ])->count();

                // Determine status: waiting if ticket OR event is full
                $status = ($ticketConfirmedCount >= $ticket->quantity
                    || $eventConfirmedCount >= $event->capacity)
                    ? 'waiting'
                    : 'registered';

                // Seed the registration record
                UserEventRegistration::factory()
                    ->{$status === 'registered' ? 'registered' : 'waiting'}()
                    ->create([
                        'user_id'           => $user->id,
                        'event_id'          => $event->id,
                        'event_ticket_id'   => $ticket->id,
                    ]);
            }
        }
    }
}
