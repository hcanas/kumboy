<?php

namespace App\Listeners;

use App\Events\StoreRequestCancel;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogStoreRequestCancel
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
     * @param  StoreRequestCancel  $event
     * @return void
     */
    public function handle(StoreRequestCancel $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->store_request->user_id,
                'date_recorded' => $event->store_request->updated_at,
                'action_taken' => 'Cancelled '
                    .str_replace('_', ' ', $event->store_request->type)
                    .' request with reference #'
                    .$event->store_request->code.'.',
            ]);
    }
}
