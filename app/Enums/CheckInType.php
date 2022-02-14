<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CheckInType extends Enum
{
    const PRESENT   = 'present';
    const ABSENT    =  'absent';
    const OFF_DUTY  = 'off_duty';

    public static function getSubType(string $value): array
    {
        switch ($value) {
            case self::PRESENT:
                return [
                    CheckInSubType::DUTY,
                    CheckInSubType::UNDER_INSTRUCTION,
                    CheckInSubType::CONFERENCE,
                    CheckInSubType::SCHOOLING,
                    CheckInSubType::OTHERS
                ];
                break;
            case self::ABSENT:
                return [
                    CheckInSubType::LEAVE,
                    CheckInSubType::CONFINED_IN_HOSPITAL,
                    CheckInSubType::SICK,
                    CheckInSubType::SUSPENDED,
                    CheckInSubType::OTHERS
                ];
                break;
            default:
                return  [];
                break;
        }
    }
}
