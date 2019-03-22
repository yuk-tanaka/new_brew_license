<?php

namespace App\Utilities\Scrapers;

use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use InvalidArgumentException;
use DB;
use Throwable;

class NewLicenseScraper extends Scraper
{
    /** @var string */
    private $nextDateUrl;

    /** @var array */
    private const AREA_PREFECTURE_LIST = [
        'sapporo' => ['sapporo'],
        'sendai' => ['aomori', 'akita', 'iwate', 'yamagata', 'miyagi', 'fukushima'],
        'kantoshinetsu' => ['ibaraki', 'tochigi', 'gumma', 'saitama', 'niigata', 'nagano'],
        'tokyo' => ['chiba', 'tokyo', 'kanagawa', 'yamanashi'],
        'nagoya' => ['gifu', 'shizuoka', 'aichi', 'mie'],
        'kanazawa' => ['toyama', 'ishikawa', 'fukui'],
        'osaka' => ['shiga', 'kyoto', 'osaka', 'hyogo', 'nara', 'wakayama'],
        'hiroshima' => ['tottori', 'shimane', 'okayama', 'hiroshima', 'yamaguchi'],
        'takamatsu' => ['tokushimasei', 'kagawasei', 'ehimesei', 'kochisei'],
        'fukuoka' => ['fukuoka', 'saga', 'nagasaki'],
        'kumamoto' => ['kumamoto', 'oita', 'miyazaki', 'kagoshima'],
        'okinawa' => ['okinawa'],
    ];

    /**
     * @param string $nextDateUrl
     * @return NewLicenseScraper
     */
    public function setNextDateUrl(string $nextDateUrl): self
    {
        $this->nextDateUrl = $nextDateUrl;
        return $this;
    }

    /**
     * @return Collection
     * @throws InvalidArgumentException
     */
    protected function getUrls(): Collection
    {
        if (is_null($this->nextDateUrl)) {
            throw new InvalidArgumentException('need to call setNextDateUrl');
        }

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
     */
    protected function crawl(Crawler $crawler): void
    {
        try {
            $crawler->filter('tbody')->first()->filter('tr')->each(function (Crawler $tr) use ($crawler) {
                //<tbody>の下に<th>が存在する局がある
                if ($tr->filter('th')->count()) {
                    return;
                };
                //該当ない場合の記述方法は局によって異なる
                if (mb_strpos($tr->text(), '該当なし') !== false) {
                    return;
                }

                $this->crawled->push([
                    'prefectureId' => $this->crawlPrefectureIdFromTitle($crawler),
                    'warekiPermittedAt' => $tr->filter('td')->eq(1)->text(),
                    'nameAndCompanyNumber' => $tr->filter('td')->eq(3)->html(),
                    'address' => $tr->filter('td')->eq(4)->text(),
                    'licenseType' => $tr->filter('td')->eq(5)->text(),
                    'drinkType' => $tr->filter('td')->eq(6)->text(),
                    'processingType' => $tr->filter('td')->eq(7)->text(),
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
                'prefecture' => $row['prefectureId'],
                'permitted_at' => $this->wareki->parse($row['warekiPermittedAt']),
                'name' => $this->formatName($row['nameAndCompanyNumber']),
                'address' => $row['address'],
                'drink_type_id' => $this->findOrCreateDrinkType($row['drinkType'])->id,
            ];
        });
    }

    /**
     * @param Collection $formatted
     * @throws Throwable
     */
    protected function createLicense(Collection $formatted): void
    {
        DB::transaction(function () use ($formatted) {
            foreach ($formatted as $row) {
                $this->license->create($row + [
                        'can_send_notification' => true,
                    ]);
            }
        });
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
     * titleから都道府県idを取得
     * @param Crawler $crawler
     * @return int
     * @throws InvalidArgumentException
     */
    private function crawlPrefectureIdFromTitle(Crawler $crawler): int
    {
        $title = $crawler->filter('title')->text();

        $id = $this->prefecture->matchToId($title);

        if (is_null($id)) {
            throw new InvalidArgumentException('titleがnull');
        }

        return $id;
    }
}