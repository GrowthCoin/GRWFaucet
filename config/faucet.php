<?php
return [
    'ticker' => env( 'COIN_TICKER',  'GRW' ),
    'coinName' => env( 'COIN_NAME', 'Growthcoin' ),
    'explorerUrlTx' => env( 'EXPLORER_URL_TX', ''),
    'faucetAddress' => env( 'FAUCET_ADDRESS', ''),
    'minReward' => env( 'MIN_REWARD', 1000000 ), // in smallest denomination (satoshis)
    'maxReward' => env( 'MAX_REWARD', 11000000), // in smallest denomination (satoshis)
    'coinDecimals' => env( 'COIN_DECIMALS', 6 ),
    'payoutCooldownSeconds' => env( 'PAYOUT_COOLDOWN_TIMER', 4 * 60 * 60 ), // in seconds, here 4 hours
    'recaptchaSiteKey' => env( 'RECAPTCHA_SITE_KEY', '' ),
    'recaptchaPrivateKey' => env( 'RECAPTCHA_PRIVATE_KEY', '' ),
    'recaptchaEnable' => env( 'RECAPTCHA_ENABLE', 0),
];
