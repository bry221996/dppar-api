<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PersonnelClassification extends Enum
{
    const REGULAR       = 'regular';
    const FLEXIBLE_TIME = 'flexible_time';
    const SOCIAL_UNIT   = 'social_unit';
    const MOBILE_FORCE  = 'mobile_force';
    const UNIT_HEAD     = 'unit_head';

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
