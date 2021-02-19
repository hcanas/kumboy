<?php

namespace App\Listeners;

use App\Events\UpdatedProduct;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUpdatedProduct
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
     * @param  UpdatedProduct  $event
     * @return void
     */
    public function handle(UpdatedProduct $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->store->user_id,
                'date_recorded' => now(),
                'action_taken' => 'Updated <product_name>'
                    .$event->product->name
                    .'</product_name> of <store_name>'
                    .$event->store->name
                    .'</store_name> store.<product_id>'
                    .$event->product->id
                    .'</product_id><store_id>'
                    .$event->store->id
                    .'</store_id>',
                'category' => 'update_product',
            ]);
    }
}
