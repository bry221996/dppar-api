<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PersonnelCategory extends Enum
{
    const UNIFORM       = 'uniform';
    const NON_UNIFORM   = 'non_uniform';

    public static function getAll()
    {
        return [self::UNIFORM, self::NON_UNIFORM];
    }
}
