<?php

namespace App\Repositories;

use App\Models\PasswordReset;

class PasswordResetRepository extends Repository
{
    public function __construct(PasswordReset $passwordReset)
    {
        parent::__construct($passwordReset);
    }
}
