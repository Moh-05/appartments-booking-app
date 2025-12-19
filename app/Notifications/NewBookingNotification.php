<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification
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
        return ['database']; // or add 'mail' if you want email notifications too
    }

    /**
     * Get the array representation of the notification.
     */
  public function toArray($notifiable)
{
    return [
        'message'           => 'New booking request submitted',
        'booking_id'        => $this->booking->id,
        'user_id'           => $this->booking->user_id, 
        'appartement_id'    => $this->booking->appartement_id,
        'appartement_title' => $this->booking->appartement->title,
        'owner_id'          => $this->booking->appartement->owner->id, 
        'start_date'        => $this->booking->start_date, 
        'end_date'          => $this->booking->end_date,
        'total_price'       => $this->booking->total_price, 
    ];
}
}