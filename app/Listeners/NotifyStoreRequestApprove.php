<?php

namespace App\Listeners;

use App\Events\StoreRequestApprove;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyStoreRequestApprove
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
     * @param  StoreRequestApprove  $event
     * @return void
     */
    public function handle(StoreRequestApprove $event)
    {
        Notification::send(
            User::query()->find($event->store_request->user_id),
            new \App\Notifications\StoreRequestApprove($event->store_request)
        );

        if ($event->store_request->type === 'store transfer') {
            $store_transfer = $event->store_request->storeTransfer()->first();

            Notification::send(
                User::query()->find($store_transfer->target_id),
                new \App\Notifications\StoreTransferred($store_transfer)
            );
        }
    }
}
