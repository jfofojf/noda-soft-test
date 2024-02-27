<?php

namespace App\Events\MailEvents;
use App\Services\MessagesClient;

abstract class MailEvent
{
    public const EVENT_NAME = 'abstract';

    protected MessagesClient $messagesClient;
    protected array $data;
    protected int $resellerId;
    protected ?int $clientId;
    protected ?int $differences;

    public function __construct(
        MessagesClient $messagesClient,
        array $data,
        int $resellerId,
        $clientId = null,
        $differences = null
    ) {
        $this->messagesClient = $messagesClient;
        $this->data = $data;
        $this->resellerId = $resellerId;
        $this->clientId = $clientId;
        $this->differences = $differences;
    }

    public static function dispatch(...$args): void
    {
        // fakes the method
    }
}