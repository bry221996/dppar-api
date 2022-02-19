<?php

namespace App\Models;

use App\Traits\WithSerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, WithSerializeDate, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getIsSuperAdminAttribute()
    {
        return $this->role === 'super_admin';
    }

    public function getIsRegionalPoliceOfficerAttribute()
    {
        return $this->role === 'regional_police_officer';
    }

    public function getIsProvincialPoliceOfficerAttribute()
    {
        return $this->role === 'provincial_police_officer';
    }

    public function getIsMunicipalPoliceOfficerAttribute()
    {
        return $this->role === 'municipal_police_officer';
    }
}
