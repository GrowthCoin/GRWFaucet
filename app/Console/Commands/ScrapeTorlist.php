<?php

namespace App\Console\Commands;

use Spatie\Crawler\Crawler;
use Illuminate\Console\Command;
use \Spatie\Crawler\CrawlObserver;

class ScrapeTorlist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:torlist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the latest Tor nodes from www.dan.me.uk/tornodes';


    protected $url = 'https://www.dan.me.uk/tornodes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Crawler::create()
            ->setCrawlObserver( new \App\Console\Commands\Observer  )
            ->setMaximumCrawlCount(1)
            ->startCrawling($this->url);
    }

}
