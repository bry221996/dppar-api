<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Personnel extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $guarded = [];

    protected $hidden = [
        'mpin',
    ];

    protected $appends = [
        'has_pin'
    ];

    public function getHasPinAttribute()
    {
        return !! $this->mpin;
    }
}
