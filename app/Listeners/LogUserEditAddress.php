<?php

namespace App\Listeners;

use App\Events\UserEditAddress;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserEditAddress
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
     * @param  UserEditAddress  $event
     * @return void
     */
    public function handle(UserEditAddress $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->address->user_id,
                'date_recorded' => now(),
                'action_taken' => $event->address->map_coordinates !== $event->oldAddress['map_coordinates']
                    ? 'Relocated address from coordinates '
                        .$event->oldAddress['map_coordinates']
                        .' to '.$event->address->map_coordinates.'.'
                    : 'Updated address on coordinates '.$event->address->map_coordinates.'.',
            ]);
    }
}
