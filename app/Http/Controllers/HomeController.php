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
        try
        {
            $balance = bitcoind()->getBalance()->get();
        } catch( \Exception $e )
        {
            $balance = null;
        }
        return view('home', [ 'faucetBalance' => $balance, 'payouts' => $payouts ] );
    }
}
