<?php

namespace App\Enums;

class StockMovementType
{
    public const RECEIVE = 'RECEIVE';
    public const ADJUST_IN = 'ADJUST_IN';
    public const ADJUST_OUT = 'ADJUST_OUT';

    public static function labels(): array
    {
        return [
            self::RECEIVE => 'Receive Stock',
            self::ADJUST_IN => 'Adjustment In',
            self::ADJUST_OUT => 'Adjustment Out',
        ];
    }

}