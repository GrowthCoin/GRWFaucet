<?php

namespace App\Http\Controllers;

use App\Address;
use App\Rules\CoolDown;
use App\Rules\ValidAddress;
use App\Rules\ValidRecaptcha;
use App\Events\NewCoinAsk;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class AddressController extends Controller
{
    public function ask(Request $request, RateLimiter $limiter)
    {
        // Throttle requests to prevent abuse
        // Throttling by IP and Address submitted
        $keyIP = $request->ip() . ':ask_submitted';
        $keyAddress = $request->input('grwaddress') . ':ask_submitted';

        // Get Tor node IP list if available.
        try {
            $ftorNodeIPs = \json_decode( \Storage::disk('local')->get('torExitNodeIPs.json') );
        } catch (\Exception $e) {
            $ftorNodeIPs = [];
        }


        if( in_array($request->ip(), $ftorNodeIPs) ) {
            return back()->with('error', 'You cannot ask from the Tor network. Please try again when you are not connected to Tor.');
        }


        if ( $limiter->tooManyAttempts($keyIP, 1) ) {
            $availableAt = now()->addSeconds($limiter->availableIn($keyIP))->ago();

            return back()->with('error', 'Try again '. $availableAt);
        }
        if ( $limiter->tooManyAttempts($keyAddress, 1) ) {
            $availableAt = now()->addSeconds($limiter->availableIn($keyAddress))->ago();

            return back()->with('error', 'Try again '. $availableAt);
        }

        // Add a hit to throttle 20 seconds
        $limiter->hit($keyIP, 20);
        $limiter->hit($keyAddress, 20);


        // Validate data
        $validatedData = $request->validate([
            'grwaddress' => ['required', new ValidAddress, new CoolDown],
            'g-recaptcha-response' => [new ValidRecaptcha]
        ]);

        // Prevent abuse from same IP
        $ip = Address::where(['ip' => $request->ip() ] )->first();
        if( ! is_null($ip) )
        {
            if( $ip->updated_at->addSeconds(config('faucet.payoutCooldownSeconds')) > \Carbon\Carbon::now() )
            {
                return back()->with('error', "Hey Captain, hold on for a minute, you just claimed some ". config('faucet.ticker')."!<br />To be fair, you can ask only once every " . \Carbon\CarbonInterval::seconds(config('faucet.payoutCooldownSeconds'))->cascade()->forHumans());
            }
        }

        // Select random amount to send
        $amount = (rand(config('faucet.minReward'), config('faucet.maxReward'))) / pow(10, config('faucet.coinDecimals'));

        // Check if amount is bigger than minimum balance or wallet balance
        $minBalance = config('faucet.minBalance') / pow(10, config('faucet.coinDecimals'));
        try
        {
            $balance = bitcoind()->getBalance()->get();
        } catch( \Exception $e )
        {
            $balance = 0;
        }
        if( $amount + $minBalance >= $balance )
        {
            return back()->with('error', 'Oh, this is embarassing! We don\'t have enough funds to complete your request.<br />Please come back later or let us know on <a href="'.config('faucet.discordUrl').'">Discord</a>');
        }


        // Last 10 minutes
        $askCount10 = Address::where('updated_at', '>=', now()->subSeconds(600) )->count();
        // Last 30 minutes
        $askCount30 = Address::where('updated_at', '>=', now()->subSeconds(1800) )->count();
        // Last 60 minutes
        $askCount60 = Address::where('updated_at', '>=', now()->subSeconds(3600) )->count();

        // Check for sustained ask. Reduce payout if so.
        if( ($askCount10/10) >= 0.8)
        {
            $amount *= 0.5;
        }
        if( ($askCount30/30) >= 0.8)
        {
            $amount *= 0.10;
        }
        if( ($askCount60/60) >= 0.8)
        {
            $amount *= 0.05;
        }

        // Retrieve model
        $address = Address::FirstOrNew(['address' => $request->grwaddress]);

        // Execute
        try
        {

            event( new NewCoinAsk( $request->grwaddress, $amount) );

            {
                $address->count += 1;
                $address->amount += $amount;
                $address->ip = $request->ip();
                $address->save();

                $message = $amount.' ' . config('faucet.ticker') . ' was queued to be sent to <strong>'. $request->grwaddress .'</strong>.<br />Your ask has been granted and will be processed shortly.';

                return back()->with('success', $message );
            }

        } catch( \Exception $e )
        {
            /**
             * DEBUG
             */
            if( config( 'app.debug' ) )
            {
                \Log::debug( "AddressController::ask() failed\n\t " .$e->getMessage() );
            }

            return back()->with('error', 'Something went wrong, please try again in a few minutes.');
        }
    }
}
