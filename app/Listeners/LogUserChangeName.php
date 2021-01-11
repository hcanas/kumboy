<?php

namespace App\Listeners;

use App\Events\UserChangeName;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserChangeName
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
     * @param  UserChangeName  $event
     * @return void
     */
    public function handle(UserChangeName $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->user->id,
                'date_recorded' => now(),
                'action_taken' => 'Changed name from '.$event->oldName.' to '.$event->user->name.'.',
            ]);
    }
}
