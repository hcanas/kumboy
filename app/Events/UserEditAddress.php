<?php

namespace App\Events;

use App\Models\UserAddressBook;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserEditAddress
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $address;

    public $oldAddress;

    /**
     * Create a new event instance.
     *
     * @param  UserAddressBook $address
     * @param $oldAddress
     * @return void
     */
    public function __construct(UserAddressBook $address, $oldAddress)
    {
        $this->address = $address;
        $this->oldAddress = $oldAddress;
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
