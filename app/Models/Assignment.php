<?php

namespace App\Models;

use App\Traits\WithSerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory, WithSerializeDate;

    protected $guarded = [];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function subUnit()
    {
        return $this->belongsTo(SubUnit::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
