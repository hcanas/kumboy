<?php

namespace App\Listeners;

use App\Events\StoreRequestCreate;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class NotifyStoreRequestCreate
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
     * @param  StoreRequestCreate  $event
     * @return void
     */
    public function handle(StoreRequestCreate $event)
    {
        Notification::send($event->store_request->status === 'approved'
            ? Auth::user()
            : User::query()
                ->whereRaw('role REGEXP "admin"')
                ->whereNull('banned_until')
                ->get(),
            new \App\Notifications\StoreRequestCreate($event->store_request)
        );

        if ($event->store_request->status === 'approved' AND $event->store_request->type === 'store transfer') {
            $store_transfer = $event->store_request->storeTransfer()->first();

            Notification::send(
                User::query()->find($store_transfer->target_id),
                new \App\Notifications\StoreTransferred($store_transfer)
            );
        }
    }
}
