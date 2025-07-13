<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTickets extends Model
{
    use HasFactory;

    protected $table = 'event_tickets';

    protected $fillable = [
        'event_id',
        'type',
        'price',
        'quantity',
    ];

    /**
     * Get the event that owns the ticket type.
     * This relationship now points to the singular 'Events' model.
     */
    public function event()
    {
        return $this->belongsTo(Events::class);
    }

    /**
     * Get the user event registrations that used this specific ticket type.
     * This implies UserEventRegistration has an 'event_ticket_id' foreign key.
     */
    public function userEventRegistrations()
    {
        return $this->hasMany(UserEventRegistration::class, 'event_ticket_id');
    }

    /**
     * Helper to get the count of confirmed registrations for this specific ticket type.
     */
    public function getConfirmedRegistrationsCountAttribute()
    {
        return $this->userEventRegistrations()->where('status', 'registered')->count();
    }

    /**
     * Helper to check if there is capacity available for this specific ticket type.
     */
    public function isAvailable()
    {
        return $this->confirmed_registrations_count < $this->quantity;
    }
}
