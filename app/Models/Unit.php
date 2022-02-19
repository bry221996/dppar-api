<?php

namespace App\Models;

use App\Traits\WithSerializeDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes, WithSerializeDate;

    protected $guarded = [];

    public function subUnits()
    {
        return $this->hasMany(SubUnit::class);
    }

    public function scopeSearch(Builder $query, string $search)
    {
        return $query->where(function ($subQuery) use ($search) {
            $subQuery->where('name', 'LIKE', "%$search%")
                ->orWhere('region', 'LIKE', "%$search%")
                ->orWhere('code', 'LIKE', "%$search%");
        });
    }
}
