<?php

namespace App\Models;

use App\Traits\WithSerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubUnit extends Model
{
    use HasFactory, SoftDeletes, WithSerializeDate;

    protected $guarded = [];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function stations()
    {
        return $this->hasMany(Station::class);
    }
}
