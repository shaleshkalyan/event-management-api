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
}
