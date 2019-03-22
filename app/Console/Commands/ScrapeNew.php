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
use DB;
use Throwable;

class ScrapeNew extends Command
{
    /**
     * @var string
     */
    protected $signature = 'scrape:new {dateUrl?}';

    /**
     * @var string
     */
    protected $description = 'scrape new license data {dateUrl: e.g."h30/01"}';

    /** @var OldLicenseScraper */
    private $scraper;

    /** @var ScrapeHistory */
    private $scrapeHistory;

    /** @var string */
    private $nextDateUrl;

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

        $this->scrapeHistory = $scrapeHistory;

        $this->nextDateUrl = $wareki->parseNextScrapingUrlString($scrapeHistory);

        $this->scraper = new NewLicenseScraper($goutteClient, $drinkType, $license, $prefecture, $wareki);
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->scraper->setNextDateUrl($this->argument('dateUrl') ?? $this->nextDateUrl);

        DB::transaction(function () {
            $this->scraper->run();

            if (is_null($this->argument('dateUrl'))) {
                $this->scrapeHistory->create(['scraped_at' => Carbon::now()]);
            }
        });
    }
}