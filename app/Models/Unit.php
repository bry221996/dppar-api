<?php

namespace App\Models;

use App\Traits\WithSerializeDate;
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
}
