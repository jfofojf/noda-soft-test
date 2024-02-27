<?php

namespace App\Enum;
class StatusEnum
{
    public const COMPLETED = 0;
    public const PENDING = 1;
    public const REJECTED = 2;
    private static array $statusMap = [
        self::COMPLETED => 'Completed',
        self::PENDING => 'Pending',
        self::REJECTED => 'Rejected',
    ];

    public static function getNameByCode(int $code): string
    {
        return self::$statusMap[$code] ?? 'Wrong code';
    }
}