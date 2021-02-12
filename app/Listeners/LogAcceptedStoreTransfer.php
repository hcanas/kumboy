<?php

namespace App\Listeners;

use App\Events\AcceptedStoreTransfer;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogAcceptedStoreTransfer
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
     * @param  AcceptedStoreTransfer  $event
     * @return void
     */
    public function handle(AcceptedStoreTransfer $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => Auth::id(),
                'date_recorded' => now(),
                'action_taken' => 'Accepted store transfer application with reference number <ref_no>'
                    .$event->store_request->ref_no.'</ref_no>.',
                'category' => $event->store_request->category,
            ]);
    }
}
