<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewCoinAsk
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Address requesting coin
    public $address;

    // Amount to be sent
    public $amount;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $address, $amount )
    {

        /**
         * DEBUG
         */
        if( config( 'app.debug' ) )
        {
            \Log::debug( "NewCoinAsk event triggered: " .$address ." requested " .$amount . " " . config('faucet.ticker') . "." );
        }

        $this->address = $address;
        $this->amount  = $amount;
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
