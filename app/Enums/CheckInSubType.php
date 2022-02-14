<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CheckInSubType extends Enum
{
    const DUTY                  = 'duty';
    const UNDER_INSTRUCTION     = 'under_instruction';
    const CONFERENCE            = 'conference';
    const SCHOOLING             = 'schooling';
    const TRAVEL                = 'travel';
    const OTHERS                = 'others';
    const LEAVE                 = 'leave';
    const CONFINED_IN_HOSPITAL  = 'confined_in_hospital';
    const SICK                  = 'sick';
    const SUSPENDED             = 'suspended';
}
