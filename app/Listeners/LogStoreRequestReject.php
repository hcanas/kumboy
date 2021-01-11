<?php

namespace App\Listeners;

use App\Events\StoreRequestReject;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogStoreRequestReject
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
     * @param  StoreRequestReject  $event
     * @return void
     */
    public function handle(StoreRequestReject $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->store_request->evaluated_by,
                'date_recorded' => $event->store_request->updated_at,
                'action_taken' => 'Rejected '
                    .str_replace('_', ' ', $event->store_request->type)
                    .' request with reference #'
                    .$event->store_request->code.'.',
            ]);
    }
}
