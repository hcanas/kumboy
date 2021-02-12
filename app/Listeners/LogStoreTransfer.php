<?php

namespace App\Listeners;

use App\Events\StoreTransfer;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogStoreTransfer
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
        UserActivity::query()
            ->create([
                'user_id' => Auth::id(),
                'date_recorded' => now(),
                'action_taken' => 'Submitted a transfer application for "'
                    .$event->store->name
                    .'" store with reference number <ref_no>'
                    .$event->store_request->ref_no
                    .'</ref_no>.',
                'category' => $event->store_request->category,
            ]);
    }
}
