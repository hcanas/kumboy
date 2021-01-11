<?php

namespace App\Notifications;

use App\Models\StoreTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreTransferred extends Notification
{
    use Queueable;

    public $store_transfer;

    /**
     * Create a new notification instance.
     *
     * @param StoreTransfer $store_transfer
     * @return void
     */
    public function __construct(StoreTransfer $store_transfer)
    {
        $this->store_transfer = $store_transfer;
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
            'store_id' => $this->store_transfer->store_id,
            'type' => 'store_received',
            'message' => 'A store ownership has been transferred to you.',
        ];
    }
}
