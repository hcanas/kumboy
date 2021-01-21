<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogActivityOrderPlaced
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
     * @param  OrderPlaced  $event
     * @return void
     */
    public function handle(OrderPlaced $event)
    {
        if (Auth::check()) {
            UserActivity::query()
                ->create([
                    'user_id' => Auth::id(),
                    'date_recorded' => now(),
                    'action_taken' => 'Placed an order with tracking number '.$event->order->tracking_number,
                ]);
        }
    }
}
