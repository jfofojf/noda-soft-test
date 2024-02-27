<?php

namespace App\Enum;

class NotificationEnum
{
    public const TYPE_NEW = 1;
    public const TYPE_CHANGE = 2;

    public static function getResellerEmailFrom(): string
    {
        return 'contractor@example.com';
    }

    public static function getEmailsByPermit(?int $resellerId = null, ?string $event = null): array
    {
        // fakes the method
        return ['someemeil@example.com', 'someemeil2@example.com'];
    }
}