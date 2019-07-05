<?php

namespace App\Jobs;

use App\PendingAsk;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPendingAsks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $processStart = now();
        $pendingAsks = PendingAsk::where( 'updated_at', '<=', $processStart )->get();

        try
        {
            foreach( $pendingAsks as $pendingAsk )
            {
                $sendTo[$pendingAsk->address] = (real) $pendingAsk->amount;
                PendingAsk::where('address', $pendingAsk->address)->delete();

            }
            $response = bitcoind()->sendmany( config('faucet.sendAccount'), $sendTo );

            if(! is_null( $response->get() ))
            {
                /**
                 * DEBUG
                 */
                if( config( 'app.debug' ) )
                {
                    \Log::debug( "ProcessCoinAsk job completed. Sent to these addresses:\n\t " .print_r($sendTo, true));
                }
            }

        } catch( \Exception $e )
        {
            /**
             * DEBUG
             */
            if( config( 'app.debug' ) )
            {
                \Log::debug( "ProcessCoinAsk job processed but something whent wrong:\n\t" .$e->getMessage() ."\n\t" . "Addresses sending to:\n\t" . print_r($sendTo, true ));
            }
        }




    }
}
