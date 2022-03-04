<?php

namespace App\Models;

use App\Enums\StatusType;
use App\Enums\UserRole;
use App\Traits\WithSerializeDate;
use Illuminate\Database\Eloquent\Builder;
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
        return $this->role === UserRole::SUPER_ADMIN;
    }

    public function getIsRegionalPoliceOfficerAttribute()
    {
        return $this->role === UserRole::REGIONAL_POLICE_OFFICER;
    }

    public function getIsProvincialPoliceOfficerAttribute()
    {
        return $this->role === UserRole::PROVINCIAL_POLICE_OFFICER;
    }

    public function getIsInactiveAttribute()
    {
        return $this->status === StatusType::INACTIVE;
    }

    public function getIsMunicipalPoliceOfficerAttribute()
    {
        return $this->role === UserRole::MUNICIPAL_POLICE_OFFICER;
    }

    public function scopeSearch(Builder $query, string $search)
    {
        return $query->where(function ($subQuery) use ($search) {
            $subQuery->where('name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%");
        });
    }

    public function classifications()
    {
        return $this->belongsToMany(Classification::class, 'user_classifications');
    }
}
