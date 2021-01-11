<?php

namespace App\Listeners;

use App\Events\StoreRequestApprove;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogStoreRequestApprove
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
     * @param  StoreRequestApprove  $event
     * @return void
     */
    public function handle(StoreRequestApprove $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->store_request->evaluated_by,
                'date_recorded' => $event->store_request->updated_at,
                'action_taken' => 'Approved '
                    .str_replace('_', ' ', $event->store_request->type)
                    .' request with reference #'
                    .$event->store_request->code.'.',
            ]);
    }
}
