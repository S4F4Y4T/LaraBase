<?php

namespace App\Models;

use App\Builders\PaginateBuilder;
use App\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Main extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*']);
    }

    public function scopeFilter(Builder $query, QueryFilter $filter): void
    {
        $filter->apply($query);
    }

    public function newEloquentBuilder($query): PaginateBuilder
    {
        return new PaginateBuilder($query);
    }
}
