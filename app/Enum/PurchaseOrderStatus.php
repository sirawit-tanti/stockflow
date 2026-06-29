<?php

namespace App\Enum;

class PurchaseOrderStatus
{
    public const DRAFT = 'DRAFT';
    public const PENDING_APPROVAL = 'PENDING_APPROVAL';
    public const APPROVED = 'APPROVED';
    public const REJECTED = 'REJECTED';
    public const PARTIALLY_RECEIVED = 'PARTIALLY_RECEIVED';
    public const COMPLETED = 'COMPLETED';
    public const CANCELLED = 'CANCELLED';

    public static function labels(): array
    {
        return [
            self::DRAFT => 'Draft',
            self::PENDING_APPROVAL => 'Pending Approval',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::PARTIALLY_RECEIVED => 'Partially Received',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        ];
    }
}