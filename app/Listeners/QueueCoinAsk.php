<?php

namespace App\Listeners;

use App\PendingAsk;
use App\Events\NewCoinAsk;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueCoinAsk
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewCoinAsk  $event
     * @return void
     */
    public function handle(NewCoinAsk $event)
    {
        $pendingAsk = PendingAsk::FirstOrNew(['address' => $event->address]);
        $pendingAsk->amount += $event->amount;

        try {
            $pendingAsk->save();
        } catch (\Exception $e) {

            /**
             * DEBUG
             */
            if( config( 'app.debug' ) )
            {
                \Log::debug( "QueueCoinAsk event listener failed\n\t " .$e->getMessage() );
            }

            // exit silently
            return;
        }
    }
}
