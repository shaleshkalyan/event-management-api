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
     * Get the event that owns the ticket.
     */
    public function event()
    {
        return $this->belongsTo(Events::class);
    }

    /**
     * Get the user event registrations for the ticket type.
     */
    public function userEventRegistrations()
    {
        return $this->hasMany(UserEventRegistration::class, 'event_ticket_id');
    }
}
