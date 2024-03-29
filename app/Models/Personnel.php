<?php

namespace App\Models;

use App\Enums\StatusType;
use App\Traits\WithSerializeDate;
use Illuminate\Database\Eloquent\Builder;
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

    protected $casts = [
        'is_intel' => 'boolean'
    ];

    public function getHasPinAttribute()
    {
        return !!$this->pin_updated_at;
    }

    public function getIsInactiveAttribute()
    {
        return $this->status === StatusType::INACTIVE;
    }

    public function assignment()
    {
        return $this->hasOne(Assignment::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function scopeSearch(Builder $query, string $search)
    {
        return $query->where(function ($subQuery) use ($search) {
            $subQuery->where('badge_no', 'LIKE', "%$search%")
                ->orWhere('first_name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%$search%")
                ->orWhere('personnel_id', 'LIKE', "%$search%")
                ->orWhere('middle_name', 'LIKE', "%$search%");
        });
    }
}
