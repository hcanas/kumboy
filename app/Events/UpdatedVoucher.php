<?php

namespace App\Events;

use App\Models\Store;
use App\Models\Voucher;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdatedVoucher
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $voucher;

    public $store;

    /**
     * Create a new event instance.
     *
     * @param Voucher $voucher
     * @param Store $store
     * @return void
     */
    public function __construct(Voucher $voucher, Store $store)
    {
        $this->voucher = $voucher;
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
