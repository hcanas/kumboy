<?php

namespace App\Listeners;

use App\Events\UserLogout;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserLogout
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
     * @param  UserLogout  $event
     * @return void
     */
    public function handle(UserLogout $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->user->id,
                'date_recorded' => now(),
                'action_taken' => 'Logged out.',
            ]);
    }
}
