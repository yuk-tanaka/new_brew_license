<?php

namespace App\Console\Commands;

use App\Eloquents\DrinkType;
use App\Eloquents\License;
use App\Utilities\Prefecture;
use App\Utilities\Scrapers\OldLicenseScraper;
use App\Utilities\Wareki;
use Goutte\Client;
use Illuminate\Console\Command;

class ScrapeOld extends Command
{
    /**
     * @var string
     */
    protected $signature = 'scrape:old';

    /**
     * @var string
     */
    protected $description = 'scrape old license data';

    /** @var OldLicenseScraper */
    private $scraper;

    /**
     * @param Client $goutteClient
     * @param DrinkType $drinkType
     * @param License $license
     * @param Prefecture $prefecture
     * @param Wareki $wareki
     */
    public function __construct(Client $goutteClient, DrinkType $drinkType, License $license, Prefecture $prefecture, Wareki $wareki)
    {
        parent::__construct();

        $this->scraper = new OldLicenseScraper($goutteClient, $drinkType, $license, $prefecture, $wareki);
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->scraper->run();
    }
}