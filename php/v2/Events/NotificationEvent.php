<?php

namespace App\V2\Events;

use App\Services\NotificationManager;

class NotificationEvent
{
    public function __construct(
        private NotificationManager $notificationManager,
    ) {}

    public static function dispatch(...$args): void
    {
        // fakes the method
    }

    public function handle()
    {
        $this->notificationManager->send();
    }
}