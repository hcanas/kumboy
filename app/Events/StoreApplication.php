<?php

namespace App\Events;

use App\Models\Store;
use App\Models\StoreRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreApplication
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $store_request;

    public $store_application;

    public $store;

    /**
     * Create a new event instance.
     *
     * @param StoreRequest $store_request
     * @param \App\Models\StoreApplication $store_application
     * @param Store|null $store
     * @return void
     */
    public function __construct(
        StoreRequest $store_request,
        \App\Models\StoreApplication $store_application,
        Store $store = null
    ) {
        $this->store_request = $store_request;
        $this->store_application = $store_application;
        $this->store = $store;
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
