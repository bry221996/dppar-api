<?php

namespace App\Models;

use App\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use HasFactory, Filterable;

    protected $guarded = [];

    protected $casts = [
        'is_accounted' => 'boolean'
    ];

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }
}
