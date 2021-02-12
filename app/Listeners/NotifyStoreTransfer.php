<?php

namespace App\Listeners;

use App\Events\StoreTransfer;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyStoreTransfer
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
     * @param  StoreTransfer  $event
     * @return void
     */
    public function handle(StoreTransfer $event)
    {
        Notification::send(
            User::query()
                ->whereIn('role', ['superadmin', 'admin'])
                ->get(),
            new \App\Notifications\StoreTransfer($event)
        );
    }
}
