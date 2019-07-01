@extends('layouts.app')

@push('css-override')
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .g-recaptcha {
            /* display: inline-block; */
        }

        button > .icon {
          fill: #dedede;
        }
    </style>
@endpush

@push('inline-script')
<script type="text/javascript">
  function copyToClipboard(text, el) {
    var copyTest = document.queryCommandSupported('copy');
    var elOriginalText = el.attr('data-original-title');

    if (copyTest === true) {
      var copyTextArea = document.createElement("textarea");
      copyTextArea.value = text;
      document.body.appendChild(copyTextArea);
      copyTextArea.select();
      try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'Copied!' : 'Whoops, not copied!';
        el.attr('data-original-title', msg).tooltip('show');
      } catch (err) {
        console.log('Oops, unable to copy');
      }
      document.body.removeChild(copyTextArea);
      el.attr('data-original-title', elOriginalText);
    } else {
      // Fallback if browser doesn't support .execCommand('copy')
      window.prompt("Copy to clipboard: Ctrl+C or Command+C, Enter", text);
    }
  }

  $(document).ready(function() {
    // Enable Tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Copy to clipboard the data-copy attribute and pass it to our copy function
    $('.js-copy').click(function() {
      var text = $(this).attr('data-copy');
      var el = $(this);
      copyToClipboard(text, el);
    });
  });
</script>
@endpush

@section('content')
<div class="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="title m-b-md">
                @if( !is_null( $faucetBalance ) )
                    Faucet balance: <br /><strong>{{ $faucetBalance }} {{ config('faucet.ticker') }}</strong>
                @else
                    No connection to the {{ config('faucet.coinName') }} network.
                @endif
            </div>
            <div class="col-md-8">
                <div class="row mb-5">
                    <div class="col-12">
                        Send some {{ config('faucet.ticker') }} here: <div class="btn-group">
                          <span id="faucet-address" data-toggle="tooltip" data-copy="{{ config('faucet.faucetAddress') }}" class="btn btn-outline-info js-copy" data-placement="bottom" title="Copy to clipboard">{{ config('faucet.faucetAddress') }}</span>
                          <button type="button" class="btn btn-dark btn-copy js-copy" data-toggle="tooltip" data-placement="bottom" data-copy="{{ config('faucet.faucetAddress') }}" title="Copy to clipboard">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="24" height="24" viewBox="0 0 24 24"><path d="M17,9H7V7H17M17,13H7V11H17M14,17H7V15H14M12,3A1,1 0 0,1 13,4A1,1 0 0,1 12,5A1,1 0 0,1 11,4A1,1 0 0,1 12,3M19,3H14.82C14.4,1.84 13.3,1 12,1C10.7,1 9.6,1.84 9.18,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3Z" /></svg>
                          </button>
                        </div> to keep this faucet running.
                    </div>
                </div>
                @if (session()->has('success'))
                    <div class="alert alert-success">
                        @if(is_array(session()->get('success')))
                        <ul class="text-left">
                            @foreach (session()->get('success') as $message)
                                <li>{!! $message !!}</li>
                            @endforeach
                        </ul>
                        @else
                            {!! session()->get('success') !!}
                        @endif
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        @if(is_array(session()->get('error')))
                        <ul class="text-left">
                            @foreach (session()->get('error') as $message)
                                <li>{!! $message !!}</li>
                            @endforeach
                        </ul>
                        @else
                            {!! session()->get('error') !!}
                        @endif
                    </div>
                @endif
                <div class="card">
                    <div class="card-header"><h2>Join our community by claiming some {{ config('faucet.ticker') }}!</h2></div>

                    <div class="card-body">
                        <h3>Enter your address and start claiming!</h3>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="text-left">
                                    @foreach ($errors->all() as $error)
                                        <li>{!! $error !!}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('ask') }}" method="POST">
                          <div class="form-group">
                            <label>{{ config('faucet.coinName') }} address</label>
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <div class="input-group-text">
                                  <i class="fa fa-chevron-right"></i>
                                </div>
                              </div>
                              <input id="grw-address" name="grwaddress" placeholder="Enter your {{ config('faucet.coinName') }} address" type="text" class="form-control">
                            </div>
                          </div>
                          <div class="form-group">
                            @if(config('faucet.recaptchaEnable'))
                                <div class="g-recaptcha flex-center" data-sitekey="{{ config('faucet.recaptchaSiteKey') }}"></div>
                            @endif
                            <button name="submit" type="submit" class="btn btn-primary">Ask</button>
                          </div>
                          @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
