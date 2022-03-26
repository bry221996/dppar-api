<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CheckInType extends Enum
{
    const PRESENT   = 'present';
    const LEAVE     = 'leave';
    const OFF_DUTY  = 'off_duty';
    const UNACCOUNTED = 'unaccounted';
    const ABSENT = 'absent';

    public static function getSubType(string $value): array
    {
        switch ($value) {
            case self::PRESENT:
                return [
                    CheckInSubType::DUTY,
                    CheckInSubType::UNDER_INSTRUCTION,
                    CheckInSubType::CONFERENCE,
                    CheckInSubType::SCHOOLING,
                    CheckInSubType::OTHERS,
                    CheckInSubType::TRAVEL
                ];
                break;
            case self::ABSENT:
                return [
                    CheckInSubType::CONFINED_IN_HOSPITAL,
                    CheckInSubType::SICK,
                    CheckInSubType::SUSPENDED,
                    CheckInSubType::DROP_FROM_ROLLS,
                    CheckInSubType::MISSING,
                    CheckInSubType::UNDER_DETENTION,
                    CheckInSubType::LOSSES,
                    CheckInSubType::TERMINATED,
                    CheckInSubType::DECEASED,
                ];
            default:
                return  [];
                break;
        }
    }

    public static function getShortCode(string $value): string
    {
        switch ($value) {
            case self::PRESENT:
                return 'P';
            case self::LEAVE:
                return 'Loa';
            case self::OFF_DUTY:
                return 'OD';
            case self::UNACCOUNTED:
                return 'UN';
            case self::ABSENT:
                return 'Abs';
            default:
                return '';
                break;
        }
    }
}
