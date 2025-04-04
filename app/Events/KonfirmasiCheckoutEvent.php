<?php

namespace App\Events;

use App\Models\Kehadiran;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KonfirmasiCheckoutEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $kehadiran;

    public function __construct(Kehadiran $kehadiran)
    {
        $this->kehadiran = $kehadiran;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('konfirmasi-checkout');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->kehadiran->id,
            'status_konfirmasi' => $this->kehadiran->status_konfirmasi,
            'booking_id' => $this->kehadiran->booking_id,
        ];
    }
}

