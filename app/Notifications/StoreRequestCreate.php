<?php

namespace App\Notifications;

use App\Models\StoreRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreRequestCreate extends Notification
{
    use Queueable;

    public $store_request;

    /**
     * Create a new notification instance.
     *
     * @param StoreRequest $store_request
     * @return void
     */
    public function __construct(StoreRequest $store_request)
    {
        $this->store_request = $store_request;
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
            'user_id' => $this->store_request->user_id,
            'code' => $this->store_request->code,
            'type' => 'store_request',
            'message' => $this->store_request->status === 'approved'
                            ? 'You have created and approved '.$this->store_request->type.' request.'
                            : $this->store_request->type.' request has been made.',
        ];
    }
}
