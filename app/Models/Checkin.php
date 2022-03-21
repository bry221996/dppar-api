<?php

namespace App\Models;

use App\Traits\WithSerializeDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use HasFactory, WithSerializeDate;

    protected $guarded = [];

    protected $casts = [
        'is_accounted' => 'boolean',
        'from_offline_sync' => 'boolean'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('ancient', function (Builder $builder) {
            $builder->where('type', '!=', 'inactive');
        });
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function taggedBy()
    {
        return $this->belongsTo(User::class, 'tagged_as_absent_by', 'id');
    }

    public function scopeSearch(Builder $query, string $search)
    {
        return $query->where(function ($subQuery) use ($search) {
            $subQuery->where('remarks', 'LIKE', "%$search%")
                ->orWhereDate('created_at', $search);
        });
    }

    public function scopePersonnel(Builder $query, string $search)
    {
        return $query->whereHas('personnel', function ($personnelQuery) use ($search) {
            return $personnelQuery->where(function ($subQuery) use ($search) {
                $subQuery->where('badge_no', 'LIKE', "%$search%")
                    ->orWhere('first_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%")
                    ->orWhere('personnel_id', 'LIKE', "%$search%")
                    ->orWhere('middle_name', 'LIKE', "%$search%");
            });
        });
    }

    public function scopeStartDate(Builder $query, $date)
    {
        return $query->whereDate('created_at', '>=', $date);
    }

    public function scopeEndDate(Builder $query, $date)
    {
        return $query->whereDate('created_at', '<=', $date);
    }
}
