<?php
return [
    'ticker' => env( 'COIN_TICKER',  'GRW' ),
    'coinName' => env( 'COIN_NAME', 'Growthcoin' ),
    'explorerUrl' => env( 'EXPLORER_URL', ''),
    'explorerUrlTxApi' => env( 'EXPLORER_URL_TX_API', ''),
    'discordUrl' => env( 'DISCORD_URL', 'https://discord.gg/pgfC2Xr'),
    'faucetAddress' => env( 'FAUCET_ADDRESS', ''),
    'sendAccount' => env( 'FAUCET_SEND_ACCOUNT', '' ),
    'minBalance' => env( 'MIN_BALANCE', 50000000 ), // in smallest denomination (satoshis)
    'minReward' => env( 'MIN_REWARD', 1000000 ), // in smallest denomination (satoshis)
    'maxReward' => env( 'MAX_REWARD', 11000000), // in smallest denomination (satoshis)
    'coinDecimals' => env( 'COIN_DECIMALS', 6 ),
    'payoutCooldownSeconds' => env( 'PAYOUT_COOLDOWN_TIMER', 4 * 60 * 60 ), // in seconds, here 4 hours
    'recaptchaSiteKey' => env( 'RECAPTCHA_SITE_KEY', '' ),
    'recaptchaPrivateKey' => env( 'RECAPTCHA_PRIVATE_KEY', '' ),
    'recaptchaEnable' => env( 'RECAPTCHA_ENABLE', 0),
];
