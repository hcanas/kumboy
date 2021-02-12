<?php

namespace App\Listeners;

use App\Events\StoreApplication;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyStoreApplication
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
     * @param  StoreApplication  $event
     * @return void
     */
    public function handle(StoreApplication $event)
    {
        Notification::send(
            User::query()
                ->whereIn('role', ['superadmin', 'admin'])
                ->get(),
            new \App\Notifications\StoreApplication($event)
        );
    }
}
