<?php

namespace App\Utilities\Scrapers;

use App\Eloquents\DrinkType;
use App\Eloquents\License;
use App\Utilities\Prefecture;
use App\Utilities\Wareki;
use Goutte\Client;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use InvalidArgumentException;
use Log;

abstract class Scraper
{
    /** @var Client */
    protected $goutteClient;

    /** @var DrinkType */
    protected $drinkType;

    /** @var License */
    protected $license;

    /** @var Prefecture */
    protected $prefecture;

    /** @var Wareki */
    protected $wareki;

    /** @var Collection */
    protected $crawled;

    /** @var Collection */
    protected $formatted;

    /**
     * Scraper constructor.
     * @param Client $goutteClient
     * @param DrinkType $drinkType
     * @param License $license
     * @param Prefecture $prefecture
     * @param Wareki $wareki
     */
    public function __construct(Client $goutteClient, DrinkType $drinkType, License $license, Prefecture $prefecture, Wareki $wareki)
    {
        $this->goutteClient = $goutteClient;
        $this->drinkType = $drinkType;
        $this->license = $license;
        $this->prefecture = $prefecture;
        $this->wareki = $wareki;
        $this->crawled = collect([]);
        $this->formatted = collect([]);
    }

    /**
     * スクレイピング
     */
    public function run(): void
    {
        foreach ($this->getUrls() as $url) {
            $crawler = $this->goutteClient->request('GET', $url);

            //newScraperでまだページができていない場合は404
            if ($this->goutteClient->getResponse()->getStatus() === 200) {
                $this->crawl($crawler);
            }
        }

        $this->format($this->crawled);

        $this->createLicense($this->formatted);
    }

    /**
     * @return Collection
     */
    abstract protected function getUrls(): Collection;

    /**
     * @param Crawler $crawler
     */
    abstract protected function crawl(Crawler $crawler): void;

    /**
     * @param Collection $crawled
     */
    abstract protected function format(Collection $crawled): void;

    /**
     * @param Collection $formatted
     */
    abstract protected function createLicense(Collection $formatted): void;

    /**
     * 1行目は法人番号or個人名
     * 法人番号は除外する
     * 案件によって1-3行
     * @param string $nameAndCompanyNumber
     * @return string
     */
    protected function formatName(string $nameAndCompanyNumber): string
    {
        $corp = mb_strpos($nameAndCompanyNumber, '法人番号');

        if ($corp !== false) {
            $corpString = mb_substr($nameAndCompanyNumber, 0, mb_strpos($nameAndCompanyNumber, '<br>'));

            $trimmed = str_replace($corpString, '', $nameAndCompanyNumber);

            return str_replace('<br>', ' ', $trimmed);
        }

        return str_replace(['<br>', '　'], ' ', $nameAndCompanyNumber);
    }

    /**
     * @param string $drinkTypeName
     * @return DrinkType
     */
    protected function findOrCreateDrinkType(string $drinkTypeName): DrinkType
    {
        return $this->drinkType->where('name', $drinkTypeName)->firstOrCreate([
            'name' => $drinkTypeName,
        ]);
    }

    /**
     * Goutteでは要素がない場合に例外を発生させる
     * @param InvalidArgumentException $e
     */
    protected function loggingWhenCrawlException(InvalidArgumentException $e): void
    {
        echo $e->getMessage();
        Log::notice($e->getMessage());
    }
}