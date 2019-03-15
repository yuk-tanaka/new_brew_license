<?php

namespace App\Eloquents;

use App\Events\LicenseCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Eloquents\License
 *
 * @property int $id
 * @property int $drink_type_id
 * @property string $prefecture
 * @property string $name
 * @property string $address
 * @property \Illuminate\Support\Carbon $permitted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Eloquents\DrinkType $drinkType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License whereDrinkTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License wherePermittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License wherePrefecture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property bool $can_send_notification
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\License whereCanSendNotification($value)
 */
class License extends Model
{
    /** @var array */
    protected $casts = [
        'drink_type_id' => 'integer',
        'prefecture' => 'integer',
        'can_send_notification' => 'boolean',
    ];

    /** @var array */
    protected $dates = [
        'permitted_at',
        'created_at',
        'updated_at',
    ];

    protected $dispatchesEvents = [
        'created' => LicenseCreated::class,
    ];

    /** @var array */
    protected $fillable = [
        'drink_type_id',
        'prefecture',
        'name',
        'address',
        'permitted_at',
        'can_send_notification',
    ];

    /** @var array 取り込む「免許等区分」 */
    public const ALLOWED_LICENSE_TYPES = ['酒類'];

    /** @var array 取り込む「処理区分」 */
    public const ALLOWED_PROCESSING_TYPES = ['新規'];

    /**
     * @return BelongsTo
     */
    public function drinkType(): BelongsTo
    {
        return $this->belongsTo(DrinkType::class);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isAllowedLicenseType(string $type): bool
    {
        return array_search($type, self::ALLOWED_LICENSE_TYPES, true) !== false;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isAllowedProcessingType(string $type): bool
    {
        return array_search($type, self::ALLOWED_PROCESSING_TYPES, true) !== false;
    }
}
