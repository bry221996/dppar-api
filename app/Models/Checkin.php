<?php

namespace App\Models;

use App\Traits\WithSerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use HasFactory, WithSerializeDate;

    protected $guarded = [];

    protected $casts = [
        'is_accounted' => 'boolean'
    ];

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }
}
