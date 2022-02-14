<?php

namespace App\Models;

use App\Traits\WithSerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurisdiction extends Model
{
    use HasFactory, WithSerializeDate;

    protected $guarded = [];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
