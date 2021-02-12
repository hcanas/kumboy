<?php

namespace App\Listeners;

use App\Events\RejectedStoreTransfer;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyRejectedStoreTransfer
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
     * @param  RejectedStoreTransfer  $event
     * @return void
     */
    public function handle(RejectedStoreTransfer $event)
    {
        Notification::send(
            User::query()
                ->find($event->store_request->user_id),
            new \App\Notifications\RejectedStoreTransfer($event)
        );
    }
}
