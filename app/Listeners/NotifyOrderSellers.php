<?php

namespace App\Listeners;

use App\Events\OrderPlaced as OrderPlacedEvent;
use App\Notifications\OrderPlaced as OrderPlacedNotification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyOrderSellers
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
     * @param  OrderPlacedEvent  $event
     * @return void
     */
    public function handle(OrderPlacedEvent $event)
    {
        Notification::send(
            User::query()
            ->whereIn('id', $event->store_owner_ids)
            ->get(),
            new OrderPlacedNotification($event->order)
        );
    }
}
