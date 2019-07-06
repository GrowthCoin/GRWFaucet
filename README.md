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

## Setup the queue worker
To run the queue worker, the `php artisan queue:work` command needs to be running continuously to check for any new jobs in the database.

The easiest way to make sure your queue worker is always active is to use [`supervisor`](http://supervisord.org/index.html)

Here's an example of a supervisor configuration for your worker. This configuration file is stored in `/etc/supervisor/conf.d/faucet-worker.conf`


```
[program:faucet-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-faucet-project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path-to-your-faucet-project/storage/logs/worker.log
```

Then simply have supervisor know about the changes

- `sudo supervisorctl reread`
- `sudo supervisorctl update`
- `sudo supervisorctl start faucet-worker:*`

Checkout the documentation to learn how to [install supervisor](http://supervisord.org/installing.html).
