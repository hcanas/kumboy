<?php

namespace App\Listeners;

use App\Events\GenericUserActivity;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogGenericUserActivity
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
     * @param GenericUserActivity $event
     * @return void
     */
    public function handle(GenericUserActivity $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => Auth::id(),
                'date_recorded' => now(),
                'action_taken' => $event->message,
                'category' => 'generic',
            ]);
    }
}
