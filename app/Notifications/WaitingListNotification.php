<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Events;
use App\Models\UserEventRegistration;
use Illuminate\Notifications\Messages\MailMessage;

class WaitingListNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $registration;

    /**
     * Create a new notification instance.
     *
     * @param Events $event The event the user was promoted for.
     * @param UserEventRegistration $registration The registration record.
     * @param string $type Can be 'promotion' or 'spot_available'.
     */
    public function __construct(Events $event, UserEventRegistration $registration)
    {
        $this->event = $event;
        $this->registration = $registration;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param object $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $eventDate = $this->event->date->format('F j, Y \a\t h:i A');
        $eventLink = url('/events/' . $this->event->id);

        $subject = "Great News! Your Waiting List Spot for '{$this->event->title}' is Confirmed!";
        $greeting = "Hello {$notifiable->name},";
        $messageLines = [
            "We're excited to inform you that your waiting list spot for the event:",
            "**{$this->event->title}**",
            "on **{$eventDate}**",
            "has been confirmed! You are now actively registered for this event.",
            "We look forward to seeing you there!"
        ];

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting);

        foreach ($messageLines as $line) {
            $mailMessage->line($line);
        }

        return $mailMessage->action('View Event Details', $eventLink);
    }

    public function toArray(object $notifiable): array
    {
        $message = "Great news! Your waiting list spot for '{$this->event->title}' on {$this->event->date->format('Y-m-d H:i')} has been confirmed. You are now actively registered!";

        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_date' => $this->event->date->format('Y-m-d H:i:s'),
            'registration_id' => $this->registration->id,
            'new_status' => $this->registration->status,
            'message' => $message,
        ];
    }
}
