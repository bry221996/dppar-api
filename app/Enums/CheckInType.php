<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CheckInType extends Enum
{
    const PRESENT   = 'present';
    const LEAVE     = 'leave';
    const OFF_DUTY  = 'off_duty';
    const UNACCOUNTED = 'unaccounted';

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
            default:
                return  [];
                break;
        }
    }
}
