<?php

namespace App\Models;

use App\Traits\WithSerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Personnel extends Authenticatable
{
    use HasFactory, HasApiTokens, WithSerializeDate, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'mpin',
        'pin_updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'has_pin'
    ];

    public function getHasPinAttribute()
    {
        return !!$this->pin_updated_at;
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
