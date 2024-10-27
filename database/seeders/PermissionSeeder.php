<?php

namespace Database\Seeders;

use App\Constants\V1\PermissionConstants;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            PermissionConstants::ROLE_SHOW,
            PermissionConstants::ROLE_CREATE,
            PermissionConstants::ROLE_UPDATE,
            PermissionConstants::ROLE_DELETE,
        ];

        // Create permissions using the create method
        foreach ($permissions as $permission) {
            Permission::query()->create([
                'name' => $permission,
            ]);
        }
    }
}
