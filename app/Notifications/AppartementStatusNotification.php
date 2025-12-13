<?php

namespace App\Notifications;

use App\Models\Appartement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppartementStatusNotification extends Notification
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
        return ['database']; // add 'mail' if you want email too
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'message'        => "Your appartement has been {$this->appartement->approval_status}",
            'appartement_id' => $this->appartement->id,
            'title'          => $this->appartement->title ?? null,
            'status'         => $this->appartement->approval_status,
            'user_id'        => $this->appartement->user_id,
        ];
    }
}