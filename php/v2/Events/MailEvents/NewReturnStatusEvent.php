<?php

namespace App\Events\MailEvents;

class NewReturnStatusEvent extends MailEvent
{
    public const EVENT_NAME = 'newReturnStatus';

    public function handle(): void
    {
        $this->messagesClient->sendMessage(
            $this->data,
            $this->resellerId,
        );
    }
}