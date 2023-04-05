<?php

namespace App\Events;

use App\Models\TestAuth;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PumpAuthorized
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	public $testAuth;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TestAuth $testAuth)
    {
        $this->testAuth = $testAuth;
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
