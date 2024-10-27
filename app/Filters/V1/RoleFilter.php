<?php

namespace App\Filters\V1;

class RoleFilter extends QueryFilter
{
    protected array $sort = ['id', 'name'];
    protected array $includes = ['permissions', 'users'];

    public function name(string $value): void
    {
        $this->builder->where('name', 'like', '%' . $value . '%');
    }

}
