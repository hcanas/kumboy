<?php

namespace App\Listeners;

use App\Events\StoreRequestReject;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyStoreRequestReject
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
     * @param  StoreRequestReject  $event
     * @return void
     */
    public function handle(StoreRequestReject $event)
    {
        Notification::send(
            User::query()
                ->where('id', $event->store_request->user_id)
                ->first(),
            new \App\Notifications\StoreRequestReject($event->store_request)
        );
    }
}
