<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Events extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'date',
        'venue',
        'capacity',
        'is_recurring',
        'recurrence_type',
    ];

    protected $casts = [
        'date' => 'datetime',
        'is_recurring' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the event tickets for the event.
     */
    public function eventTickets()
    {
        return $this->hasMany(EventTickets::class, 'event_id', 'id');
    }

    /**
     * Get the user event registrations for the event.
     */
    public function userEventRegistrations()
    {
        return $this->hasMany(UserEventRegistration::class, 'event_id', 'id');
    }

    /**
     * Helper to get the count of confirmed registrations for this event.
     * Note: This now uses the 'userEventRegistrations' relationship.
     */
    public function getCurrentConfirmedCapacityAttribute()
    {
        return $this->userEventRegistrations()->where('status', 'registered')->count();
    }

    /**
     * Helper to check if there is overall capacity available for the event.
     * Note: This now uses the 'userEventRegistrations' relationship.
     */
    public function isCapacityAvailable()
    {
        return $this->current_confirmed_capacity < $this->capacity;
    }

    /**
     * Optional: A more advanced check for specific ticket type availability.
     * This assumes you have 'regular' and 'VIP' ticket types defined in EventTickets.
     * You'll need to adapt your EventTickets model and migration for this.
     */
    public function isTicketTypeAvailable(string $ticketType)
    {
        // Get the total quantity available for this ticket type from EventTickets
        $totalTicketsForType = $this->eventTickets()
                                    ->where('type', $ticketType)
                                    ->sum('quantity');

        // Get the count of confirmed registrations for this specific ticket type
        $confirmedForType = $this->userEventRegistrations()
                                 ->where('status', 'registered')
                                 ->where('ticket_type', $ticketType)
                                 ->count();

        return $confirmedForType < $totalTicketsForType;
    }
}
