<?php

namespace App\Listeners;

use App\Events\RejectedStoreTransfer;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogRejectedStoreTransfer
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
     * @param  RejectedStoreTransfer  $event
     * @return void
     */
    public function handle(RejectedStoreTransfer $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => Auth::id(),
                'date_recorded' => now(),
                'action_taken' => 'Rejected store transfer application with reference number <ref_no>'
                    .$event->store_request->ref_no.'</ref_no>.',
                'category' => $event->store_request->category,
            ]);
    }
}
