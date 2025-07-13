<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEventRegistration extends Model
{
    use HasFactory;

    protected $table = 'user_event_registrations';
    protected $fillable = [
        'user_id',
        'event_id',
        'event_ticket_id',
        'status',          // e.g., 'confirmed', 'waiting_list', 'cancelled'
        'registered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the user that made the registration.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event associated with the registration.
     */
    public function event()
    {
        return $this->belongsTo(Events::class);
    }

    /**
     * Get the specific event ticket type associated with the registration.
     */
    public function eventTicket()
    {
        return $this->belongsTo(EventTickets::class, 'event_ticket_id');
    }
}