<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    public $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database']; // you can add 'mail' if you want email too
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'message'    => "Your booking has been {$this->booking->status}",
            'booking_id' => $this->booking->id,
            'start date' => $this->booking->start_date,
            'status'     => $this->booking->status,
            'end_date'   => $this->booking->end_date,
            'appartement_title' => $this->booking->appartement->title 
        ];
    }
}