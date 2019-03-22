<?php

namespace App\Utilities\Scrapers;

use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use InvalidArgumentException;
use DB;
use Throwable;

class OldLicenseScraper extends Scraper
{
    /** @var array */
    private const URL_LIST = [
        'h30/12/01',
        'h30/12/02',
        'h30/12/03',
        'h30/12/04',
        'h30/12/05',
        'h30/12/06',
        'h30/12/07',
    ];

    /**
     * @return Collection
     */
    protected function getUrls(): Collection
    {
        return collect(self::URL_LIST)->map(function ($url) {
            return $this->buildUrl($url);
        });
    }

    /**
     * @param Crawler $crawler
     */
    protected function crawl(Crawler $crawler): void
    {
        try {
            $crawler->filter('tbody')->first()->filter('tr')->each(function (Crawler $tr) use ($crawler) {
                $this->crawled->push([
                    'noSuffixPrefecture' => $tr->filter('td')->eq(0)->text(),
                    'warekiPermittedAt' => $tr->filter('td')->eq(2)->text(),
                    'nameAndCompanyNumber' => $tr->filter('td')->eq(4)->html(),
                    'address' => $tr->filter('td')->eq(5)->text(),
                    'licenseType' => $tr->filter('td')->eq(6)->text(),
                    'drinkType' => $tr->filter('td')->eq(7)->text(),
                    'processingType' => $tr->filter('td')->eq(8)->text(),
                ]);
            });

        } catch (InvalidArgumentException $e) {
            $this->loggingWhenCrawlException($e);
        }
    }

    /**
     * @param Collection $crawled
     */
    protected function format(Collection $crawled): void
    {
        $this->formatted = $crawled->filter(function ($row) {
            return $this->license->isAllowedLicenseType($row['licenseType'])
                && $this->license->isAllowedProcessingType($row['processingType']);

        })->map(function ($row) {
            return [
                'prefecture' => $this->prefecture->toId($row['noSuffixPrefecture'], false),
                'permitted_at' => $this->wareki->parse($row['warekiPermittedAt']),
                'name' => $this->formatName($row['nameAndCompanyNumber']),
                'address' => $row['address'],
                'drink_type_id' => $this->findOrCreateDrinkType($row['drinkType'])->id,
            ];
        });
    }

    /**
     * 実質createMany
     * @param Collection $formatted
     * @throws Throwable
     */
    protected function createLicense(Collection $formatted): void
    {
        DB::transaction(function () use ($formatted) {
            foreach ($formatted as $row) {
                $this->license->create($row + [
                        'can_send_notification' => false,
                    ]);
            }
        });
    }

    /**
     * @param string $url
     * @return string
     */
    private function buildUrl(string $url): string
    {
        return 'https://www.nta.go.jp/taxes/sake/menkyo/shinki/seizo/02/' . $url . '.htm';
    }
}