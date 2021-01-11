<?php

namespace App\Listeners;

use App\Events\StoreRequestCreate;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogStoreRequestCreate
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
     * @param  StoreRequestCreate  $event
     * @return void
     */
    public function handle(StoreRequestCreate $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->store_request->user_id,
                'date_recorded' => $event->store_request->created_at,
                'action_taken' => 'Created '
                    .($event->store_request->status === 'approved' ? ' and approved ' : '')
                    .$event->store_request->type
                    .' request with reference #'
                    .$event->store_request->code.'.',
            ]);
    }
}
