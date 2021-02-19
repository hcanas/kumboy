<?php

namespace App\Notifications;

use App\Events\AutoAcceptedStoreTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecipientStoreTransfer extends Notification
{
    use Queueable;

    public $event;

    /**
     * Create a new notification instance.
     *
     * @param \App\Events\AcceptedStoreTransfer|AutoAcceptedStoreTransfer $event
     * @return void
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->event->store_request->user_id,
            'ref_no' => $this->event->store_request->ref_no,
            'message' => '"'
                .$this->event->store->name.'" store has been transferred to you with reference number '
                .$this->event->store_request->ref_no
                .'.',
            'category' => $this->event->store_request->category,
        ];
    }
}
