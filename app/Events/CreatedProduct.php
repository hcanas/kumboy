<?php

namespace App\Events;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreatedProduct
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $store;

    public $product;

    /**
     * Create a new event instance.
     *
     * @param Store $store
     * @param Product $product
     * @return void
     */
    public function __construct(Store $store, Product $product)
    {
        $this->store = $store;
        $this->product = $product;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
