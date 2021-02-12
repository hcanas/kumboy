<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreApplication extends Notification
{
    use Queueable;

    public $event;

    /**
     * Create a new notification instance.
     *
     * @param \App\Events\StoreApplication $event
     * @return void
     */
    public function __construct(\App\Events\StoreApplication $event)
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
        if ($this->event->store_request->category === 'new_store') {
            $message = 'A new application has been submitted for "'
                .$this->event->store_application->name
                .'" store with reference number '
                .$this->event->store_request->ref_no.'.';
        } elseif ($this->event->store_request->category === 'update_store') {
            $message = 'An update application has been submitted for "'
                .$this->event->store->name
                .'" store with reference number '
                .$this->event->store_request->ref_no.'.';
        }

        return [
            'user_id' => $this->event->store_request->user_id,
            'ref_no' => $this->event->store_request->ref_no,
            'message' => $message,
            'category' => $this->event->store_request->category,
        ];
    }
}
