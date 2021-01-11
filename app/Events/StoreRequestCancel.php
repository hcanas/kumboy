<?php

namespace App\Events;

use App\Models\StoreRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreRequestCancel
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $store_request;

    /**
     * Create a new event instance.
     *
     * @param StoreRequest $store_request
     * @return void
     */
    public function __construct(StoreRequest $store_request)
    {
        $this->store_request = $store_request;
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
