<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
}
