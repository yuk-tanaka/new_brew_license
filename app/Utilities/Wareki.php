<?php

namespace App\Utilities;

use App\Eloquents\ScrapeHistory;
use Carbon\Carbon;
use InvalidArgumentException;
use Throwable;

/**
 * TODO 元号変わると定義変わりそう、とりあえず平成前提かつ元年は検討しない
 * Class Wareki
 * @package App\Utilities
 */
class Wareki
{
    /** @var array */
    private const WAREKI_LIST = ['平成'];

    /**
     * @param string $gengou
     * @param int $warekiYear
     * @param int $month
     * @param int $day
     * @return Carbon
     */
    public function convert(string $gengou, int $warekiYear, int $month, int $day): Carbon
    {
        return Carbon::create($this->calcADYear($gengou, $warekiYear), $month, $day);
    }

    /**
     * 平成yy年m月d日
     * @param string $wareki
     * @return Carbon
     */
    public function parse(string $wareki): Carbon
    {
        //先頭2文字を元号とする
        $gengou = mb_substr($wareki, 0, 2);

        try {
            //3文字以降を年月日に分割
            $replaced = preg_replace('/[^\d]+/', ',', mb_substr($wareki, 2));

            list($warekiYear, $month, $day) = explode(',', $replaced);

            return $this->convert($gengou, (int)$warekiYear, (int)$month, (int)$day);

        } catch (Throwable $e) {
            throw new InvalidArgumentException('和暦文字列の値が不正 error:' . $e->getMessage());
        }
    }

    /**
     * 国税庁urlの一部
     * h30/01のような形式
     * @param ScrapeHistory $scrapeHistory
     * @return string
     */
    public function parseNextScrapingUrlString(ScrapeHistory $scrapeHistory): string
    {
        //履歴ない場合は今年1月
        $last = optional($scrapeHistory->new())->scraped_at ?? Carbon::today()->firstOfYear();

        $next = $last->firstOfMonth()->addMonth();

        return 'h' . $this->calcWarekiYear($next->year) . '/' . $next->format('m');
    }

    /**
     * @param string $gengou
     * @param int $warekiYear
     * @return int
     */
    private function calcADYear(string $gengou, int $warekiYear): int
    {
        if (!in_array($gengou, self::WAREKI_LIST, true)) {
            throw new InvalidArgumentException('元号の値が不正');
        }

        return $warekiYear + 1988;
    }

    /**
     * @param int $year
     * @return int TODO 元年あればint|string
     */
    private function calcWarekiYear(int $year): int
    {
        //平成
        return $year - 1988;
    }
}