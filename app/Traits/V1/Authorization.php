<?php

namespace App\Traits\V1;

trait Authorization
{
    protected static function detachPermissions(): void
    {
        static::deleting(function ($model) {
            $model->permissions()->detach();
        });
    }

    public function assignPermission(array|string $permissions): void
    {
        if (empty($permissions)) {
            return;
        }

        // Ensure $permissions is always an array
        $permissions = is_array($permissions) ? $permissions : [$permissions];

        // Retrieve permission models by their names
        $permissionIds = Permission::whereIn('name', $permissions)->pluck('id')->toArray();

        // Sync permissions with the role
        $this->permissions()->sync($permissionIds);
    }

    private function checkAbility(string $ability): bool
    {
        return $this->permissions->contains('name', $ability);
    }

    public function hasPermission($abilities): bool
    {
        // Ensure $abilities is an array for consistent handling
        $abilities = is_array($abilities) ? $abilities : [$abilities];

        // Check if the user has all the abilities
        foreach ($abilities as $ability) {
            if ($this->checkAbility($ability)) {
                return true; // Return false if any ability is missing
            }
        }

        return false;
    }


}
