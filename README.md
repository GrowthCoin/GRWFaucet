# GRWFaucet
Growthcoin official faucet source

## Links
- The testnet faucet can be found here: https://testnet-faucet.growthco.in/
- The mainnet faucet can be found here: https://faucet.growthco.in/

## Installation

- Clone this repo. ie. `git clone https://github.com/GrowthCoin/GRWFaucet.git faucet && cd faucet`
- Copy `.env.example` file and edit for your needs `cp -a .env.example .env`
- Install dependencies.
-- On development `composer install`
-- On production `composer install --no-dev`
- Once your DB details are in order. Issue the command `php artisan migrate`  to build the database schema.  

## Updating
In the project folder, issue `git pull` then `composer install`. On production, use `composer install --no-dev`

## Setup scheduling
Laravel can handle scheduled tasks internally, but you still need to edit your crontab to add one global entry that will manage all your tasks.

This is the entry you need in order to enable the scheduler.

```
* * * * * cd /path-to-your-faucet-project && php artisan schedule:run >> /dev/null 2>&1
```
This entry will run every minutes and check for any scheduled task in your application.  If a task is set to run at the time of check, it will be executed, if not it will be ignored.
