<?php

namespace App\Eloquents;

use App\Utilities\Wareki;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Eloquents\ScrapeHistory
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $scraped_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\ScrapeHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\ScrapeHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\ScrapeHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\ScrapeHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\ScrapeHistory whereScrapedAt($value)
 * @mixin \Eloquent
 */
class ScrapeHistory extends Model
{
    /**
     * @var array
     */
    protected $dates = [
        'scraped_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'scraped_at',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return ScrapeHistory|null
     */
    public function new(): ?self
    {
        return $this->query()->orderBy('scraped_at', 'desc')->first();
    }
}
