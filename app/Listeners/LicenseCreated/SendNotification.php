<?php

namespace App\Listeners\LicenseCreated;

use App\Eloquents\NotifiedUser;
use App\Eloquents\ScrapeHistory;
use App\Events\LicenseCreated;
use App\Notifications\LicenseCreatedNotification;
use InvalidArgumentException;

class SendNotification
{
    /**@var NotifiedUser */
    private $notifiedUser;

    /** @var ScrapeHistory|null */
    private $scrapeHistory;

    /**
     * @param NotifiedUser $notifiedUser
     * @param ScrapeHistory $scrapeHistory
     */
    public function __construct(NotifiedUser $notifiedUser, ScrapeHistory $scrapeHistory)
    {
        $this->notifiedUser = $notifiedUser;
        $this->scrapeHistory = $scrapeHistory->new();
    }

    /**
     * @param LicenseCreated $event
     * @return void
     */
    public function handle(LicenseCreated $event)
    {
        if (!env('can_send_notification')) {
            return;
        }

        if (!$event->license->can_send_notification) {
            return;
        }

        if (is_null($this->scrapeHistory)) {
            throw new InvalidArgumentException('$scrapeHistory->new()の結果がnull');
        }

        foreach ($this->notifiedUser->all() as $notified) {
            $notified->notify(new LicenseCreatedNotification($event->license, $this->scrapeHistory));
        }
    }
}
