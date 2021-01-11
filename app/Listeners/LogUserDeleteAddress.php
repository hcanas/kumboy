<?php

namespace App\Listeners;

use App\Events\UserDeleteAddress;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserDeleteAddress
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
     * @param  UserDeleteAddress  $event
     * @return void
     */
    public function handle(UserDeleteAddress $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->address->user_id,
                'date_recorded' => now(),
                'action_taken' => 'Deleted address on coordinates '.$event->address->map_coordinates.'.',
            ]);
    }
}
