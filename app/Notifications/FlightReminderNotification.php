<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Flight;

class FlightReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $flight;

    /**
     * Create a new notification instance.
     */
    public function __construct(Flight $flight)
    {
        $this->flight = $flight;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Flight Reminder')
            ->line('Hello ' . $notifiable->first_name . ',')
            ->line('This is a reminder for your flight:')
            ->line('Flight Number: ' . $this->flight->number)
            ->line('From: ' . $this->flight->departure_city)
            ->line('To: ' . $this->flight->arrival_city)
            ->line('Departure Time: ' . $this->flight->departure_time->format('Y-m-d H:i'))
            ->line('Thank you for choosing our airline!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
