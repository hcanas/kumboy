<?php

namespace App\Listeners;

use App\Events\UserChangePassword;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserChangePassword
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
     * @param  UserChangePassword  $event
     * @return void
     */
    public function handle(UserChangePassword $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->user->id,
                'date_recorded' => now(),
                'action_taken' => 'Changed password.',
            ]);
    }
}
