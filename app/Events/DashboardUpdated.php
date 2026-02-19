<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DashboardUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $idDepen;

    public function __construct(int $idDepen)
    {
        $this->idDepen = $idDepen;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('dashboard.dependencia.' . $this->idDepen),
            new Channel('dashboard.admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'DashboardUpdated';
    }
}
