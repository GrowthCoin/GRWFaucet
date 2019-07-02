<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GRWFaucet') }}</title>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('css-override')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'GRWFaucet') }}
                </a>
                Lifetime payouts: {{ $payouts }} {{ config('faucet.ticker') }}
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="https://growthco.in">Homepage</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://explorer.growthco.in">Explorer</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://discord.gg/pgfC2Xr">Join us on Discord!</a>
                        </li>
                        <!-- Authentication Links -->
                        {{-- @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest --}}
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <footer class="page-footer font-small mt-5 pt-4">

      <div class="container text-center text-md-left">
        <div class="row">
          <div class="col-md-6 mt-md-0 mt-3">
            <h5 class="text-uppercase">{{ config('app.name') }} </h5>
            <p>This faucet is operated by the Growthcoin devs.</p>
            <h6 class="text-uppercase">Stats</h6>
            <ul class="list-unstyled">
                <li>
                    Lifetime payouts: {{ $payouts }} {{ config('faucet.ticker') }}
                </li>
                <li>
                    Claiming addresses: {{ $payoutCount }}
                </li>
                <li>
                    Network connections: <?= is_null( $connections ) ? "Not connected" : $connections ?>
                </li>
                <li>
                    Block height: <?= is_null( $blocks ) ? "Not connected" : $blocks ?>
                </li>
            </ul>
          </div>

          <hr class="clearfix w-100 d-md-none pb-3">

          <div class="col-md-3 mb-md-0 mb-3">
            <h5 class="text-uppercase">Navigation</h5>
            <ul class="list-unstyled">
              <li>
                <a href="https://growthco.in">Growthcoin's homepage</a>
              </li>
              <li>
                <a href="https://explorer.growthco.in">Growthcoin's explorer</a>
              </li>
            </ul>

          </div>

          <div class="col-md-3 mb-md-0 mb-3">

            <h5 class="text-uppercase">Social</h5>
            <ul class="list-unstyled">
              <li>
                <a href="https://twitter.com/GRWcoin">Twitter</a>
              </li>
              <li>
                <a href="https://discord.gg/pgfC2Xr">Discord</a>
              </li>
              <li>
                <a href="https://bitcointalk.org/index.php?topic=641241">Bitcointalk</a>
              </li>
              <li>
                <a href="https://github.com/GrowthCoin">Github</a>
              </li>
            </ul>

          </div>
        </div>
      </div>

      <div class="footer-copyright text-center py-4  ml-1 mr-1">
          <p>
              © Copyright 2019 - GRWFaucet | <a href="https://growthco.in/">Growthcoin</a> since 2013
          </p>
      </div>
    </footer>
    @stack('inline-script')
</body>
</html>
