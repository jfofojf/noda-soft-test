<?php

namespace App\Events\MailEvents;

class ChangeReturnStatusEvent extends MailEvent
{
    public const EVENT_NAME = 'changeReturnStatus';

    public function handle(): void
    {
        $this->messagesClient->sendMessage(
            $this->data,
            $this->resellerId,
            $this->clientId,
            $this->differences
        );
    }
}