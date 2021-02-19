<?php

namespace App\Listeners;

use App\Events\AutoAcceptedStoreTransfer;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogAutoAcceptedStoreTransfer
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
     * @param  AutoAcceptedStoreTransfer  $event
     * @return void
     */
    public function handle(AutoAcceptedStoreTransfer $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => Auth::id(),
                'date_recorded' => now(),
                'action_taken' => 'Transferred '
                    .$event->store->name
                    .' store with reference number <ref_no>'
                    .$event->store_request->ref_no
                    .'</ref_no>.',
                'category' => $event->store_request->category,
            ]);
    }
}
