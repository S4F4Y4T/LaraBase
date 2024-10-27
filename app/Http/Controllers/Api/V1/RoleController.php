<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\V1\Roles\CreateRoleAction;
use App\Actions\V1\Roles\UpdateRoleAction;
use App\Filters\V1\RoleFilter;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\V1\Role\StoreRoleRequest;
use App\Http\Requests\V1\Role\UpdateRoleRequest;
use App\Http\Resources\V1\RoleResource;
use App\Models\Role;
use App\Traits\V1\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;

class RoleController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(RoleFilter $filters): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->isAuthorized('all', Role::class);

        return RoleResource::collection(
            Role::query()->filter($filters)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(StoreRoleRequest $request, CreateRoleAction $action): \Illuminate\Http\JsonResponse
    {
        $this->isAuthorized('create', Role::class);

        $role = $action($request->validated());

        return self::success(message: "Role created successfully", data: $role);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): RoleResource
    {
        $this->isAuthorized('show', $role);

        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action): \Illuminate\Http\JsonResponse
    {
        $this->isAuthorized('update', $role);

        $role = $action($role, $request->validated());

        return self::success(message: "Role updated successfully", data: $role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): \Illuminate\Http\JsonResponse
    {
        $this->isAuthorized('delete', $role);

        $role->delete();

        return self::success(message: "Role deleted successfully");
    }
}
