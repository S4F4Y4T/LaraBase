<?php

namespace App\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected Builder $builder;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder): void
    {
        $this->builder = $builder;

        if($this->request->has('filters') && is_array($this->request->get('filters')) && !empty($this->request->get('filters'))) {
            $this->filter($this->request->get('filters'));
        }

        if($this->request->has('sort') && !empty($this->request->get('sort'))) {
            $this->sort($this->request->get('sort'));
        }

        if($this->request->has('includes') && !empty($this->request->get('includes'))) {
            $this->includes($this->request->get('includes'));
        }

    }

    private function filter(array $filters): void
    {
        foreach ($filters as $key => $value) {
            if(method_exists($this, $key)){
                $this->$key($value);
            }
        }
    }

    private function sort(string $sort = ''): void
    {
        $sorts = explode(',',$sort);

        foreach ($sorts as $sort) {

            // Determine the direction (ascending by default)
            $direction = 'asc';

            // Check if the direction is specified (e.g., '-date' for descending)
            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $sort = substr($sort, 1); // Remove the '-' sign
            }

            if(!in_array($sort, $this->sort) && !array_key_exists($sort, $this->sort)) {
                continue;
            }

            $column = $this->sort[$sort] ?? $sort;

            // Apply sorting to the query
            $this->builder->orderBy($column, $direction);
        }
    }

    private function includes(string $includes): void
    {
        // Check if the string is empty
        if (empty($includes)) {
            return;
        }

        // Explode the string into an array, trim spaces, and convert to lowercase
        $includesArray = array_map('trim', explode(',', strtolower($includes)));

        // Collect valid relationships to eager load
        $relationsToEagerLoad = [];

        foreach ($includesArray as $relation) {
            // Only load valid relationships present in the $this->includes array
            if (in_array($relation, $this->includes)) {
                $relationsToEagerLoad[] = $relation;
            }
        }

        // Eager load all valid relationships at once using array
        if (!empty($relationsToEagerLoad)) {
            $this->builder->with($relationsToEagerLoad);
        }
    }
}
