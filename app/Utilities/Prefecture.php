<?php

namespace App\Utilities;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class Prefecture
{
    /** @var Collection */
    private $prefectureCollection;

    /** @var array */
    private const PREFECTURES = [
        1 => '北海道',
        2 => '青森県',
        3 => '岩手県',
        4 => '宮城県',
        5 => '秋田県',
        6 => '山形県',
        7 => '福島県',
        8 => '茨城県',
        9 => '栃木県',
        10 => '群馬県',
        11 => '埼玉県',
        12 => '千葉県',
        13 => '東京都',
        14 => '神奈川県',
        15 => '新潟県',
        16 => '富山県',
        17 => '石川県',
        18 => '福井県',
        19 => '山梨県',
        20 => '長野県',
        21 => '岐阜県',
        22 => '静岡県',
        23 => '愛知県',
        24 => '三重県',
        25 => '滋賀県',
        26 => '京都府',
        27 => '大阪府',
        28 => '兵庫県',
        29 => '奈良県',
        30 => '和歌山県',
        31 => '鳥取県',
        32 => '島根県',
        33 => '岡山県',
        34 => '広島県',
        35 => '山口県',
        36 => '徳島県',
        37 => '香川県',
        38 => '愛媛県',
        39 => '高知県',
        40 => '福岡県',
        41 => '佐賀県',
        42 => '長崎県',
        43 => '熊本県',
        44 => '大分県',
        45 => '宮崎県',
        46 => '鹿児島県',
        47 => '沖縄県'
    ];

    /**
     * @return array
     */
    static public function getKeys(): array
    {
        return array_keys(self::PREFECTURES);
    }

    /**
     * Prefecture constructor.
     */
    public function __construct()
    {
        $this->prefectureCollection = colle ct(self::PREFECTURES);
    }

    /**
     * @param string $pref
     * @param bool $hasSuffix
     * @return int
     */
    public function toId(string $pref, bool $hasSuffix = true): int
    {
        if ($hasSuffix) {
            $id = $this->prefectureCollection->search($pref, true);

        } else {
            //都府県の文字を削除
            $prefectures = $this->prefectureCollection->map(function ($pref) {
                if ($pref === '北海道') {
                    return $pref;
                }
                return str_replace(mb_substr($pref, -1), '', $pref);
            });

            $id = $prefectures->search($pref, true);
        }

        if (!$id) {
            throw new InvalidArgumentException('都道府県名が不正');
        }

        return $id;
    }

    /**
     * @param int $id
     * @return string
     */
    public function toString(int $id): string
    {
        $pref = $this->prefectureCollection->get($id);

        if (!$pref) {
            throw new InvalidArgumentException('都道府県idが不正');
        }

        return $pref;
    }

    /**
     * 複数都道府県が含まれる文字列の場合は、はじめに出てきたやつを返す
     * 北海道東京都青森県: return 北海道
     * suffix付き限定
     * @param string|null $subject
     * @return string|null
     */
    public function match(?string $subject): ?string
    {
        if (is_null($subject)) {
            return null;
        }

        return $this->prefectureCollection->filter(function ($v) use ($subject) {
            return mb_strstr($subject, $v) !== false;
        })->first();
    }

    /**
     * @param string|null $subject
     * @return int|null
     */
    public function matchToId(?string $subject): ?int
    {
        $matched = $this->match($subject);

        if (is_null($matched)) {
            return null;
        }

        return $this->toId($matched);
    }
}