<?php

namespace App\Listeners;

use App\Events\UserAddAddress;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserAddAddress
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
     * @param  UserAddAddress  $event
     * @return void
     */
    public function handle(UserAddAddress $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->address->user_id,
                'date_recorded' => now(),
                'action_taken' => 'Added address on coordinates '.$event->address->map_coordinates.'.',
            ]);
    }
}
