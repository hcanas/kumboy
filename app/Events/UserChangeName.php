<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserChangeName
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $oldName;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param array $oldName
     * @return void
     */
    public function __construct(User $user, $oldName)
    {
        $this->user = $user;
        $this->oldName = $oldName;
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
