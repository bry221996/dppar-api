<?php

namespace App\Models;

use App\Traits\WithSerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubUnit extends Model
{
    use HasFactory, WithSerializeDate;

    protected $guarded = [];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
