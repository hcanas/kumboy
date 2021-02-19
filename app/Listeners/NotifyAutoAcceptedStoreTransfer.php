<?php

namespace App\Listeners;

use App\Events\AutoAcceptedStoreTransfer;
use App\Models\User;
use App\Notifications\RecipientStoreTransfer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAutoAcceptedStoreTransfer
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
     * @param  AutoAcceptedStoreTransfer  $event
     * @return void
     */
    public function handle(AutoAcceptedStoreTransfer $event)
    {
        Notification::send(
            User::query()
                ->find($event->store_transfer->target_id),
            new RecipientStoreTransfer($event)
        );
    }
}
