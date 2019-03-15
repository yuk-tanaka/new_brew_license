<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Eloquents\DrinkType
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Eloquents\License[] $licenses
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\DrinkType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\DrinkType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\DrinkType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\DrinkType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\DrinkType whereName($value)
 * @mixin \Eloquent
 */
class DrinkType extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return HasMany
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }
}
