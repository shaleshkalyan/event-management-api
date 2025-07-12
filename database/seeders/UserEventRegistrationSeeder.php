<?php

namespace Database\Seeders;

use App\Models\Events;
use App\Models\User;
use App\Models\UserEventRegistration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserEventRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some existing users, events, and tickets
        $users = User::all();
        $events = Events::all();

        foreach ($events as $event) {
            // Get available tickets for this event
            $eventTickets = $event->eventTickets;

            if ($eventTickets->isEmpty()) {
                continue; // Skip if no tickets
            }

            // Register a few random users for each event
            $usersToRegister = $users->random(min(5, $users->count()));

            foreach ($usersToRegister as $user) {
                // Pick a random ticket type for the event
                $randomTicket = $eventTickets->random();

                // Check if a registration already exists (due to unique constraint)
                $existingRegistration = UserEventRegistration::where('user_id', $user->id)
                                                             ->where('event_id', $event->id)
                                                             ->first();

                if (!$existingRegistration) {
                    try {
                        UserEventRegistration::factory()
                            ->registered()
                            ->create([
                                'user_id' => $user->id,
                                'event_id' => $event->id,
                                'event_ticket_id' => $randomTicket->id,
                            ]);
                        if ($randomTicket->quantity > 0) {
                            $randomTicket->decrement('quantity');
                        }
                    } catch (\Illuminate\Database\QueryException $e) {
                        if (str_contains($e->getMessage(), 'Duplicate entry')) {
                            $this->command->warn("User {$user->id} already registered for event {$event->id}. Skipping.");
                        } else {
                            throw $e;
                        }
                    }
                }
            }
            if ($event->userEventRegistrations()->where('status', 'registered')->count() >= $event->capacity * 0.8) {
                $waitingUsers = $users->except($usersToRegister->pluck('id'))->random(min(2, $users->count() - $usersToRegister->count()));
                foreach ($waitingUsers as $user) {
                    $existingRegistration = UserEventRegistration::where('user_id', $user->id)
                                                             ->where('event_id', $event->id)
                                                             ->first();
                    if (!$existingRegistration) {
                        try {
                            UserEventRegistration::factory()
                                ->waiting()
                                ->create([
                                    'user_id' => $user->id,
                                    'event_id' => $event->id,
                                    'event_ticket_id' => $eventTickets->random()->id,
                                ]);
                        } catch (\Illuminate\Database\QueryException $e) {
                            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                                $this->command->warn("User {$user->id} already on waiting list for event {$event->id}. Skipping.");
                            } else {
                                throw $e;
                            }
                        }
                    }
                }
            }
        }
    }
}
