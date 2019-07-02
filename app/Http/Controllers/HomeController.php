<?php

namespace App\Http\Controllers;

use App\Address;
use App\FaucetConfig;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $payouts = Address::all()->sum('amount');
        $payoutsCount = Address::all()->count();

        try
        {
            $balance = bitcoind()->getBalance()->get();
            $connections = bitcoind()->getConnectioncount()->get();
            $blocks = bitcoind()->getBlockcount()->get();
        } catch( \Exception $e )
        {
            $balance = null;
            $connections = null;
            $blocks = null;
        }
        return view('home', [
            'faucetBalance' => $balance,
            'payouts' => $payouts,
            'payoutCount' => $payoutsCount,
            'connections' => $connections,
            'blocks' => $blocks,
        ]);
    }
}
