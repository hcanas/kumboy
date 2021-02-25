<?php

namespace App\Listeners;

use App\Events\UpdatedVoucher;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogUpdatedVoucher
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
     * @param  UpdatedVoucher  $event
     * @return void
     */
    public function handle(UpdatedVoucher $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => Auth::id(),
                'date_recorded' => now(),
                'action_taken' => 'Updated '
                    .$event->voucher->code
                    .' voucher for <store_name>'
                    .$event->store->name
                    .'</store_name> store. <store_id>'
                    .$event->store->id
                    .'</store_id>',
                'category' => 'update_voucher',
            ]);
    }
}
