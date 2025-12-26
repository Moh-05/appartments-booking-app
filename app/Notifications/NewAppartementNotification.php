<?php

namespace App\Notifications;

use App\Models\Appartement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAppartementNotification extends Notification
{
    use Queueable;

    public $appartement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appartement $appartement)
    {
        $this->appartement = $appartement;
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
        'message'        => "New appartement submitted for approval",
        'appartement_id' => $this->appartement->id,
        'title'          => $this->appartement->title ?? null,
        'owner'          => $this->appartement->owner?->username,
        'status'         => $this->appartement->approval_status,
    ];
}
}