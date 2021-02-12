<?php

namespace App\Listeners;

use App\Events\AcceptedStoreApplication;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogAcceptedStoreApplication
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
     * @param  AcceptedStoreApplication  $event
     * @return void
     */
    public function handle(AcceptedStoreApplication $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => Auth::id(),
                'date_recorded' => now(),
                'action_taken' => 'Accepted store application with reference number <ref_no>'
                    .$event->store_request->ref_no.'</ref_no>.',
                'category' => $event->store_request->category,
            ]);
    }
}
