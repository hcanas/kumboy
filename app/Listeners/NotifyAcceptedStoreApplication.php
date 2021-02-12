<?php

namespace App\Listeners;

use App\Events\AcceptedStoreApplication;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAcceptedStoreApplication
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
     * @param  AcceptedStoreApplication  $event
     * @return void
     */
    public function handle(AcceptedStoreApplication $event)
    {
        Notification::send(
            User::query()
                ->find($event->store_request->user_id),
            new \App\Notifications\AcceptedStoreApplication($event)
        );
    }
}
