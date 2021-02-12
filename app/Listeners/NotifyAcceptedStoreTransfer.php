<?php

namespace App\Listeners;

use App\Events\AcceptedStoreTransfer;
use App\Models\User;
use App\Notifications\RecipientStoreTransfer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAcceptedStoreTransfer
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AcceptedStoreTransfer  $event
     * @return void
     */
    public function handle(AcceptedStoreTransfer $event)
    {
        Notification::send(
            User::query()
                ->find($event->store_request->user_id),
            new \App\Notifications\AcceptedStoreTransfer($event)
        );

        Notification::send(
            User::query()
                ->find($event->store_transfer->target_id),
            new RecipientStoreTransfer($event)
        );
    }
}
