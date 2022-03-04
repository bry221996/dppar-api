<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PersonnelClassification extends Enum
{
    const REGULAR      = 1;
    const FLEXIBLE_TIME = 2;
    const SOCIAL_UNIT   = 3;
    const MOBILE_FORCE  = 4;
    const UNIT_HEAD     = 5;

    public static function getAll()
    {
        return [
            self::REGULAR,
            self::FLEXIBLE_TIME,
            self::SOCIAL_UNIT,
            self::MOBILE_FORCE,
            self::UNIT_HEAD
        ];
    }
}
