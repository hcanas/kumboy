<?php

namespace App\Listeners;

use App\Events\CancelledStoreTransfer;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogCancelledStoreTransfer
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
     * @param  CancelledStoreTransfer  $event
     * @return void
     */
    public function handle(CancelledStoreTransfer $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => Auth::id(),
                'date_recorded' => now(),
                'action_taken' => 'Cancelled store transfer application with reference number <ref_no>'
                    .$event->store_request->ref_no.'</ref_no>.',
                'category' => $event->store_request->category,
            ]);
    }
}
