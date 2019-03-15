<?php

namespace App\Console\Commands;

use App\Eloquents\DrinkType;
use App\Eloquents\License;
use App\Eloquents\ScrapeHistory;
use App\Utilities\Prefecture;
use App\Utilities\Scrapers\NewLicenseScraper;
use App\Utilities\Scrapers\OldLicenseScraper;
use App\Utilities\Wareki;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Console\Command;

class ScrapeNew extends Command
{
    /**
     * @var string
     */
    protected $signature = 'scrape:new';

    /**
     * @var string
     */
    protected $description = 'scrape new license data';

    /** @var OldLicenseScraper */
    private $scraper;

    /**
     * @param Client $goutteClient
     * @param DrinkType $drinkType
     * @param License $license
     * @param Prefecture $prefecture
     * @param Wareki $wareki
     * @param ScrapeHistory $scrapeHistory
     */
    public function __construct(Client $goutteClient, DrinkType $drinkType, License $license, Prefecture $prefecture, Wareki $wareki, ScrapeHistory $scrapeHistory)
    {
        parent::__construct();

        $nextDateUrl = $wareki->parseNextScrapingUrlString($scrapeHistory);

        $this->scraper = new NewLicenseScraper($goutteClient, $drinkType, $license, $prefecture, $wareki, $nextDateUrl);
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->scraper->run();
    }
}