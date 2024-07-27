<?php

namespace App\Events;

use App\Models\SensorRecord;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewRecordSend implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * NewRecordSend constructor
     *
     * @param SensorRecord $sensorRecord
     */
    public function __construct(private SensorRecord $sensorRecord, private $data)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('site.'.$this->sensorRecord->sensor->site_id),
        ];
    }

    /**
     * broadcast's event name
     *
     * @return string
     */
    public function broadcastAs(): string{
        return 'record.sent';
    }

    /**
     * data sending back to client
     * @return array
     */
    public function broadcastWith():array{
        return [
            'newRecord' => $this->data
        ];
    }
}
