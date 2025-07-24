<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusProximityEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $message;

    /**
     * Create a new event instance.
     *
     * @param int $userId - The ID of the parent to notify
     * @param string $message - The notification message
     */
    public function __construct($userId, $message)
    {
        $this->userId = $userId;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [new PrivateChannel('bus-updates.' . $this->userId)];
    }

    /**
     * Get the event name that will be broadcasted.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'bus.proximity';
    }
}
