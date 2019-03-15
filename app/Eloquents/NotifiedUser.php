<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * App\Eloquents\NotifiedUser
 *
 * @property int $id
 * @property string|null $line_key
 * @property string|null $slack_key
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\NotifiedUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\NotifiedUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\NotifiedUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\NotifiedUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\NotifiedUser whereLineKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\NotifiedUser whereSlackKey($value)
 * @mixin \Eloquent
 * @property string $line_token
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Eloquents\NotifiedUser whereLineToken($value)
 */
class NotifiedUser extends Model
{
    use Notifiable;

    /**
     * @var array
     */
    protected $fillable = [
        'line_token',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return string
     */
    protected function routeNotificationForLine(): string
    {
        return $this->line_token;
    }
}