<?php

namespace App\Events;

use App\Eloquents\License;

class LicenseCreated extends Event
{
    /** @var License */
    public $license;

    /**
     * @param License $license
     */
    public function __construct(License $license)
    {
        $this->license = $license;
    }
}