<?php

namespace App\Filters\V1;

class UserFilter extends QueryFilter
{
    protected array $sort = ['id', 'verifiedAt' => 'email_verified_at', 'name'];
    protected array $includes = ['roles', 'roles.permissions'];


    public function name(string $value): void
    {
        $this->builder->where('name', 'like', '%' . $value . '%');
    }

    public function email(string $value): void
    {
        $emails = explode(',', $value);
        $this->builder->whereIn('email', $emails);
    }

}
