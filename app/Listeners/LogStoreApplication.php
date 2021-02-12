<?php

namespace App\Listeners;

use App\Events\StoreApplication;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogStoreApplication
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
     * @param  StoreApplication  $event
     * @return void
     */
    public function handle(StoreApplication $event)
    {
        if ($event->store_request->category === 'new_store') {
            $action = 'Submitted a new application for "'
            .$event->store_application->name
            .'" store with reference number <ref_no>'
            .$event->store_application->ref_no
            .'</ref_no>.';
        } elseif ($event->store_request->category === 'update_store') {
            $action = 'Submitted an update application for "'
                .$event->store->name
                .'" store with reference number <ref_no>'
                .$event->store_application->ref_no
                .'</ref_no>.';
        }

        UserActivity::query()
            ->create([
                'user_id' => $event->store_request->user_id,
                'date_recorded' => now(),
                'action_taken' => $action,
                'category' => $event->store_request->category,
            ]);
    }
}
