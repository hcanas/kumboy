<?php

namespace App\Listeners;

use App\Events\AutoAcceptedStoreApplication;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogAutoAcceptedStoreApplication
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
     * @param  AutoAcceptedStoreApplication  $event
     * @return void
     */
    public function handle(AutoAcceptedStoreApplication $event)
    {
        if ($event->store_request->category === 'new_store') {
            $action = 'Added '.$event->store_application->name.' store with reference number <ref_no>'
                .$event->store_request->ref_no.'</ref_no>.';
        } elseif ($event->store_request->category === 'update_store') {
            $action = 'Updated '.$event->store_application->name.' store with reference number <ref_no>'
                .$event->store_request->ref_no.'</ref_no>.';
        }

        UserActivity::query()
            ->create([
                'user_id' => Auth::id(),
                'date_recorded' => now(),
                'action_taken' => $action,
                'category' => $event->store_request->category,
            ]);
    }
}
