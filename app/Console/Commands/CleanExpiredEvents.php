<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Events;
use App\Traits\ApiResponse;
use Illuminate\Support\Carbon;

class CleanExpiredEvents extends Command
{
    use ApiResponse;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:clean-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivates events whose end date has passed.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting to clean up expired events...');
        $this->logActivity('Artisan command: events:clean-expired started.');

        try {
            $now = Carbon::now();

            $expiredEvents = Events::where('date', '<', $now)
                                   ->get();

            $deactivatedCount = 0;
            foreach ($expiredEvents as $event) {
                $deactivatedCount++;
                $this->info("Deactivated event: {$event->title} (ID: {$event->id})");
                $this->logActivity("Event deactivated: ID {$event->id}, Name: {$event->title}");
            }

            if ($deactivatedCount > 0) {
                $this->info("Successfully deactivated {$deactivatedCount} expired events.");
                $this->logActivity("Successfully deactivated {$deactivatedCount} expired events.");
            } else {
                $this->info("No expired events found to deactivate.");
                $this->logActivity("No expired events found to deactivate.");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('An error occurred while cleaning expired events: ' . $e->getMessage());
            $this->logActivity('Error in events:clean-expired command: ' . $e->getMessage(), ['exception' => $e]);
            return Command::FAILURE;
        }
    }
}
