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
class SellerToUser implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $data;

   public function __construct($data, User $user)
   {
       $this->user = $user;
       $this->data = $data;
   }


    public function broadcastWith () {
        return [
            'message' => $this->data,
            'user' => $this->user->id,
        ];
    }


    public function broadcastOn()
    {
        return new PrivateChannel('seller-to-user-message.'.$this->user->id);
    }
}
