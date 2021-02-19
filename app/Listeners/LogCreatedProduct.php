<?php

namespace App\Listeners;

use App\Events\CreatedProduct;
use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogCreatedProduct
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
     * @param  CreatedProduct  $event
     * @return void
     */
    public function handle(CreatedProduct $event)
    {
        UserActivity::query()
            ->create([
                'user_id' => $event->store->user_id,
                'date_recorded' => now(),
                'action_taken' => 'Added <product_name>'
                    .$event->product->name
                    .'</product_name> as new product for <store_name>'
                    .$event->store->name
                    .'</store_name> store.<product_id>'
                    .$event->product->id
                    .'</product_id><store_id>'
                    .$event->store->id
                    .'</store_id>',
                'category' => 'new_product',
            ]);
    }
}
