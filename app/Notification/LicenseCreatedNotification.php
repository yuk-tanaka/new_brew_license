<?php

namespace App\Notifications;

use App\Eloquents\License;
use App\Eloquents\ScrapeHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Hinaloe\LineNotify\Message\LineMessage;

class LicenseCreatedNotification extends Notification
{
    use Queueable;

    /**
     * @var License
     */
    private $license;
    /**
     * @var ScrapeHistory
     */
    private $scrapeHistory;

    /**
     * NewLicenseCreated constructor.
     * @param License $license
     * @param ScrapeHistory $scrapeHistory
     */
    public function __construct(License $license, ScrapeHistory $scrapeHistory)
    {
        $this->license = $license;
        $this->scrapeHistory = $scrapeHistory;
    }

    /**
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['line'];
    }

    /**
     * @param mixed $notifable callee instance
     * @return LineMessage
     */
    public function toLine($notifable): LineMessage
    {
        return (new LineMessage())
            ->message($this->scrapeHistory->scraped_at->format('Y-m-d').'に新規免許登録')
            ->message('会社名：'.$this->license->name);
    }
}