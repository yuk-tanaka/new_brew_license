<?php

namespace App\Utilities\Scrapers;

use App\Eloquents\DrinkType;
use App\Eloquents\License;
use App\Utilities\Prefecture;
use App\Utilities\Wareki;
use Goutte\Client;
use Illuminate\Support\Collection;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler;
use InvalidArgumentException;

class NewLicenseScraper extends Scraper
{
    /** @var string */
    private $nextDateUrl;

    /** @var array */
    private const AREA_PREFECTURE_LIST = [
        'sapporo' => ['sapporo'],
        'sendai' => ['aomori', 'akita', 'iwate', 'yamagata', 'miyagi', 'fukushima'],
    ];

    /**
     * NewLicenseScraper constructor.
     * @param Client $goutteClient
     * @param DrinkType $drinkType
     * @param License $license
     * @param Prefecture $prefecture
     * @param Wareki $wareki
     * @param string $nextDateUrl
     */
    public function __construct(Client $goutteClient, DrinkType $drinkType, License $license, Prefecture $prefecture, Wareki $wareki, string $nextDateUrl)
    {
        parent::__construct($goutteClient, $drinkType, $license, $prefecture, $wareki);

        $this->nextDateUrl = $nextDateUrl;
    }

    /**
     * @return Collection
     */
    protected function getUrls(): Collection
    {
        $urls = collect([]);

        foreach (self::AREA_PREFECTURE_LIST as $area => $prefectures) {
            foreach ($prefectures as $prefecture) {
                $urls->push($this->buildUrl($this->nextDateUrl, $area, $prefecture));
            }
        }

        return $urls;
    }


    /**
     * @param Crawler $crawler
     * @param Response $response
     */
    protected function crawl(Crawler $crawler): void
    {

    }

    /**
     * @param Collection $crawled
     */
    protected function format(Collection $crawled): void
    {

    }

    /**
     * @param Collection $formatted
     */
    protected function createLicense(Collection $formatted): void
    {

    }

    /**
     * @param string $dateUrl
     * @param string $area
     * @param string $prefecture
     * @return string
     */
    private function buildUrl(string $dateUrl, string $area, string $prefecture): string
    {
        return 'https://www.nta.go.jp/about/organization/'
            . $area
            . '/sake/menkyo/seizo/data/'
            . $dateUrl
            . '/'
            . $prefecture
            . '.htm';
    }

    /**
     * titleから都道府県名を取得
     * @param Crawler $crawler
     * @return string
     * @throws InvalidArgumentException
     */
    private function crawlPrefectureFromTitle(Crawler $crawler): string
    {
        $title = $crawler->filter('title')->text();
        $prefix = '酒類等製造免許の新規取得者名等一覧（';

        preg_match('/^' . $prefix . '.{3,4})/', $title, $matches);

        dd($matches);

        return str_replace($prefix, '', $matches[0]);
    }
}