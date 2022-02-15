<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class GenderType extends Enum
{
    const MALE   = 'male';
    const FEMALE = 'female';

    public static function getAll()
    {
        return [self::MALE, self::FEMALE];
    }
}
