<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class PaginateBuilder extends Builder
{
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $total = null): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $perPage = request()->input('per_page', $perPage ?: 15);
        if ($perPage == -1) {
            return $this->get($columns);
        }
        return parent::paginate($perPage, $columns, $pageName, $page);
    }
}
