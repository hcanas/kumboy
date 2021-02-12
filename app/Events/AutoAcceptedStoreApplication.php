<?php

namespace App\Events;

use App\Models\StoreApplication;
use App\Models\StoreRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AutoAcceptedStoreApplication
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $store_request;

    public $store_application;

    /**
     * Create a new event instance.
     *
     * @param StoreRequest $store_request
     * @param StoreApplication $store_application
     * @return void
     */
    public function __construct(StoreRequest $store_request, StoreApplication $store_application)
    {
        $this->store_request = $store_request;
        $this->store_application = $store_application;
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
