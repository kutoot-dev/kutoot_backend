<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
class UserToSellerMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $data;

    public function __construct($data, User $user)
    {
        $this->data = $data;
        $this->user = $user;

    }

    public function broadcastWith () {
        return [
            'message' => $this->data,
            'user' => $this->user->id,
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user-to-seller-message.'.$this->user->id);
    }
}
