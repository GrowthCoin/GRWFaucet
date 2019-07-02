<?php

namespace App\Http\Controllers;

use App\Address;
use App\Rules\CoolDown;
use App\Rules\ValidAddress;
use App\Rules\ValidRecaptcha;
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


        // Retrieve model
        $address = Address::FirstOrNew(['address' => $request->grwaddress]);


        // Execute
        try
        {
            $response = bitcoind()->sendtoaddress( $request->grwaddress, $amount );

            if(! is_null( $response->get() ))
            {
                $address->count += 1;
                $address->amount += $amount;
                $address->ip = $request->ip();
                $address->save();

                $message = 'Sent '.$amount.' ' . config('faucet.ticker') . ' to <strong>'. $request->grwaddress .'</strong>';
                if( !empty( config('faucet.explorerUrlTx') ) )
                    $message .= ' | Track on the <a href="'. config('faucet.explorerUrlTx').$response->get() .'">explorer</a>.';

                return back()->with('success', $message );
            }

        } catch( \Exception $e )
        {
            return back()->with('error', 'Something went wrong, please try again in a few minutes.');
        }
    }
}
