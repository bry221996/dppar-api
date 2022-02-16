<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserRole extends Enum
{
    const SUPER_ADMIN               = 'super_admin';
    const REGIONAL_POLICE_OFFICER   = 'regional_police_officer';
    const PROVINCIAL_POLICE_OFFICER = 'provincial_police_officer';
    const MUNICIPAL_POLICE_OFFICER  = 'municipal_police_officer';
}
