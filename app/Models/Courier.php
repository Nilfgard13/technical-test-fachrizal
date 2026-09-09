<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'address',
        'level',
        'status',
        'joined_at',
    ];

    protected $casts = [
        'level' => 'integer',
        'joined_at' => 'date:Y-m-d',
    ];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        $keywords = preg_split('/\s+/', trim($search));

        return $query->where(function (Builder $query) use ($keywords) {
            foreach ($keywords as $keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }
        });
    }

    public function scopeFilterLevel(Builder $query, ?string $level): Builder
    {
        if (!$level) {
            return $query;
        }

        $levels = array_filter(
            array_map('intval', explode(',', $level)),
            fn($value) => $value >= 1 && $value <= 5
        );

        if (empty($levels)) {
            return $query;
        }

        return $query->whereIn('level', $levels);
    }
}
